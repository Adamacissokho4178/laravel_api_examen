<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eleve;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EleveController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'date_naissance' => 'required|date',
            'classe_id' => 'required|exists:classes,id',
            'justificatif' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        // 1. Upload fichier justificatif
        $chemin = $request->file('justificatif')->store('justificatifs', 'public');

        // 2. Génération d'un mot de passe aléatoire
        $password = Str::random(8);

        // 3. Création de l'utilisateur lié
        $user = User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'eleve',
        ]);

        // 4. Création de l'élève
        $eleve = Eleve::create([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'date_naissance' => $request->date_naissance,
            'classe_id' => $request->classe_id,
            'chemin_document' => $chemin,
            'utilisateur_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Élève enregistré avec succès.',
            'identifiants' => [
                'email' => $user->email,
                'mot_de_passe_temporaire' => $password,
            ],
            'eleve' => $eleve,
        ]);
    }
}
