<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ParentModel;
use App\Models\User;
use App\Models\Eleve;

class ParentController extends Controller
{
    public function index()
    {
        try {
            $parents = ParentModel::with(['utilisateur', 'enfants'])->get();
            return response()->json($parents);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des parents'], 500);
        }
    }

    public function show($id)
    {
        try {
            $parent = ParentModel::with(['utilisateur', 'enfants.classe'])->findOrFail($id);
            return response()->json($parent);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Parent non trouvé'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'telephone' => 'nullable|string|max:20',
                'adresse' => 'nullable|string|max:500',
                'enfants_ids' => 'required|array|min:1',
                'enfants_ids.*' => 'exists:eleves,id'
            ]);

            // 1. Génération mot de passe temporaire
            $passwordTemp = Str::random(8);

            // 2. Création de l'utilisateur lié
            $user = User::create([
                'name' => $request->prenom . ' ' . $request->nom,
                'email' => $request->email,
                'password' => Hash::make($passwordTemp),
                'role' => 'parent',
            ]);

            // 3. Création du parent
            $parent = ParentModel::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'utilisateur_id' => $user->id,
            ]);

            // 4. Génération identifiant automatique
            $identifiant = 'PAR' . date('Y') . str_pad($parent->id, 5, '0', STR_PAD_LEFT);
            $parent->identifiant = $identifiant;
            $parent->save();

            // 5. Lier les enfants au parent
            $enfants = Eleve::whereIn('id', $request->enfants_ids)->get();
            $parent->enfants()->attach($request->enfants_ids, [
                'relation' => 'parent',
                'est_principal' => true
            ]);

            return response()->json([
                'message' => 'Parent enregistré avec succès.',
                'identifiants' => [
                    'email' => $user->email,
                    'mot_de_passe_temporaire' => $passwordTemp,
                    'identifiant' => $identifiant,
                ],
                'parent' => $parent->load('enfants'),
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erreur de validation',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création d\'un parent: ' . $e->getMessage());
            return response()->json([
                'error' => 'Une erreur est survenue lors de la création du parent',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $parent = ParentModel::findOrFail($id);
            
            $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $parent->utilisateur_id,
                'telephone' => 'nullable|string|max:20',
                'adresse' => 'nullable|string|max:500',
            ]);

            $parent->update($request->all());
            
            // Mettre à jour l'utilisateur associé
            if ($parent->utilisateur) {
                $parent->utilisateur->update([
                    'name' => $request->prenom . ' ' . $request->nom,
                    'email' => $request->email,
                ]);
            }

            return response()->json([
                'message' => 'Parent mis à jour avec succès',
                'parent' => $parent->load('enfants')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $parent = ParentModel::findOrFail($id);
            
            // Supprimer l'utilisateur associé
            if ($parent->utilisateur) {
                $parent->utilisateur->delete();
            }
            
            $parent->delete();
            
            return response()->json(['message' => 'Parent supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }

    // Méthodes spécifiques pour les parents connectés
    public function mesEnfants(Request $request)
    {
        try {
            // Récupérer le parent connecté
            $user = $request->user();
            if ($user->role !== 'parent') {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $parent = ParentModel::where('utilisateur_id', $user->id)->first();
            if (!$parent) {
                return response()->json(['error' => 'Parent non trouvé'], 404);
            }

            $enfants = $parent->enfants()->with(['classe', 'notes.matiere'])->get();

            return response()->json([
                'parent' => $parent,
                'enfants' => $enfants
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des enfants'], 500);
        }
    }

    public function bulletinEnfant(Request $request, $eleveId)
    {
        try {
            // Récupérer le parent connecté
            $user = $request->user();
            if ($user->role !== 'parent') {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $parent = ParentModel::where('utilisateur_id', $user->id)->first();
            if (!$parent) {
                return response()->json(['error' => 'Parent non trouvé'], 404);
            }

            // Vérifier que l'élève appartient bien au parent
            if (!$parent->peutAccederA($eleveId)) {
                return response()->json(['error' => 'Accès non autorisé à cet élève'], 403);
            }

            $eleve = Eleve::with(['classe', 'notes.matiere.enseignant'])
                         ->findOrFail($eleveId);

            // Calculer les moyennes par trimestre
            $bulletins = [];
            $trimestres = ['T1', 'T2', 'T3'];
            
            foreach ($trimestres as $trimestre) {
                $notes = $eleve->notes()->where('periode', $trimestre)->get();
                $moyenne = $notes->count() > 0 ? $notes->avg('note') : 0;
                
                $bulletins[] = [
                    'trimestre' => $trimestre,
                    'moyenne' => round($moyenne, 2),
                    'notes' => $notes,
                    'nombre_notes' => $notes->count()
                ];
            }

            return response()->json([
                'eleve' => $eleve,
                'bulletins' => $bulletins
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération du bulletin'], 500);
        }
    }

    public function telechargerBulletin(Request $request, $eleveId, $trimestre)
    {
        try {
            // Récupérer le parent connecté
            $user = $request->user();
            if ($user->role !== 'parent') {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $parent = ParentModel::where('utilisateur_id', $user->id)->first();
            if (!$parent) {
                return response()->json(['error' => 'Parent non trouvé'], 404);
            }

            // Vérifier que l'élève appartient bien au parent
            if (!$parent->peutAccederA($eleveId)) {
                return response()->json(['error' => 'Accès non autorisé à cet élève'], 403);
            }

            $eleve = Eleve::with(['classe', 'notes.matiere.enseignant'])
                         ->findOrFail($eleveId);

            // Générer le PDF du bulletin (simulation pour l'instant)
            $bulletinData = [
                'eleve' => $eleve,
                'trimestre' => $trimestre,
                'notes' => $eleve->notes()->where('periode', $trimestre)->get(),
                'moyenne' => $eleve->notes()->where('periode', $trimestre)->avg('note')
            ];

            // TODO: Implémenter la génération PDF réelle
            return response()->json([
                'message' => 'Bulletin généré avec succès',
                'data' => $bulletinData
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la génération du bulletin'], 500);
        }
    }
}
