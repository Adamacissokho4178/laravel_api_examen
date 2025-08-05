<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Eleve;
use App\Models\User;
use App\Models\Classe;

class EleveController extends Controller
{
    public function index()
    {
        try {
            $eleves = Eleve::with('classe')->get();
            return response()->json($eleves);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des élèves'], 500);
        }
    }

    public function show($id)
    {
        try {
            $eleve = Eleve::with('classe')->findOrFail($id);
            return response()->json($eleve);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Élève non trouvé'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'prenom' => 'required|string|max:255',
                'nom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'date_naissance' => 'required|date|before_or_equal:today',
                'classe_nom' => 'required|string|exists:classes,nom',
                'chemin_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            ]);

            // Trouver la classe par son nom
            $classe = Classe::where('nom', $request->classe_nom)->first();
            if (!$classe) {
                return response()->json(['error' => 'Classe non trouvée'], 404);
            }

            // 1. Upload justificatif avec gestion d'erreur
            if (!$request->hasFile('chemin_document')) {
                return response()->json(['error' => 'Aucun fichier n\'a été fourni'], 400);
            }

            $file = $request->file('chemin_document');
            if (!$file->isValid()) {
                return response()->json(['error' => 'Le fichier uploadé n\'est pas valide'], 400);
            }

            // Générer un nom unique pour le fichier
            $fileName = time() . '_' . $file->getClientOriginalName();
            $chemin = $file->storeAs('documents', $fileName, 'public');

            if (!$chemin) {
                return response()->json(['error' => 'Erreur lors de l\'upload du document'], 500);
            }

            // 2. Génération mot de passe temporaire
            $passwordTemp = Str::random(8);

            // 3. Création de l'utilisateur lié
            $user = User::create([
                'name' => $request->prenom . ' ' . $request->nom,
                'email' => $request->email,
                'password' => Hash::make($passwordTemp),
                'role' => 'eleve',
            ]);

            // 4. Création de l'élève
            $eleve = Eleve::create([
                'prenom' => $request->prenom,
                'nom' => $request->nom,
                'email' => $request->email,
                'date_naissance' => $request->date_naissance,
                'classe_id' => $classe->id,
                'chemin_document' => $chemin,
                'utilisateur_id' => $user->id,
            ]);

            // 5. Génération identifiant automatique
            $identifiant = 'EL' . date('Y') . str_pad($eleve->id, 5, '0', STR_PAD_LEFT);
            $eleve->identifiant = $identifiant;
            $eleve->save();

            return response()->json([
                'message' => 'Élève enregistré avec succès.',
                'identifiants' => [
                    'email' => $user->email,
                    'mot_de_passe_temporaire' => $passwordTemp,
                    'identifiant' => $identifiant,
                ],
                'eleve' => $eleve,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erreur de validation',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création d\'un élève: ' . $e->getMessage());
            return response()->json([
                'error' => 'Une erreur est survenue lors de la création de l\'élève',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $eleve = Eleve::findOrFail($id);
            
            $request->validate([
                'prenom' => 'required|string|max:255',
                'nom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $eleve->utilisateur_id,
                'date_naissance' => 'required|date',
                'classe_id' => 'required|integer|exists:classes,id',
            ]);

            $eleve->update($request->all());
            
            // Mettre à jour l'utilisateur associé
            if ($eleve->utilisateur) {
                $eleve->utilisateur->update([
                    'name' => $request->prenom . ' ' . $request->nom,
                    'email' => $request->email,
                ]);
            }

            return response()->json([
                'message' => 'Élève mis à jour avec succès',
                'eleve' => $eleve->load('classe')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $eleve = Eleve::findOrFail($id);
            
            // Supprimer le document associé
            if ($eleve->chemin_document && Storage::exists($eleve->chemin_document)) {
                Storage::delete($eleve->chemin_document);
            }
            
            // Supprimer l'utilisateur associé
            if ($eleve->utilisateur) {
                $eleve->utilisateur->delete();
            }
            
            $eleve->delete();
            
            return response()->json(['message' => 'Élève supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }

    public function downloadDocument($id)
    {
        $eleve = Eleve::findOrFail($id);

        if (!$eleve->chemin_document || !Storage::exists($eleve->chemin_document)) {
            return response()->json(['error' => 'Document non trouvé'], 404);
        }

        return Storage::download($eleve->chemin_document);
    }
}
