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
    public function downloadDocument($id)
    {
        $eleve = Eleve::findOrFail($id);

        if (!$eleve->chemin_document || !Storage::exists($eleve->chemin_document)) {
            return response()->json(['error' => 'Document non trouvé'], 404);
        }

        return Storage::download($eleve->chemin_document);
    }

    public function store(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'date_naissance' => 'required|date',
            'classe_nom' => 'required|string|exists:classes,nom',
            'chemin_document' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        // Trouver la classe par son nom
        $classe = Classe::where('nom', $request->classe_nom)->first();

        // 1. Upload justificatif
        $chemin = $request->file('chemin_document')->store('documents', 'public');

        // 2. Génération mot de passe temporaire
        $passwordTemp = Str::random(8);

        // 3. Création de l’utilisateur lié
        $user = User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => Hash::make($passwordTemp),
            'role' => 'eleve',
        ]);

        // 4. Création de l’élève
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
    }
}
