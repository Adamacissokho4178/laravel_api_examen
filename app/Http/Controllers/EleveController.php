<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EleveController extends Controller
{
    public function downloadDocument($id)
    {
        $eleve = \App\Models\Eleve::findOrFail($id);

        if (!$eleve->chemin_document || !Storage::exists($eleve->chemin_document)) {
            return response()->json(['error' => 'Document non trouvé'], 404);
        }

        return Storage::download($eleve->chemin_document);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:eleves,email',
            'date_naissance' => 'required|date',
            'classe_nom' => 'required|string|exists:classes,nom',
            'chemin_document' => 'nullable|file|mimes:pdf,jpg,png',
        ]);

        // Trouver la classe par son nom
        $classe = \App\Models\Classe::where('nom', $validated['classe_nom'])->first();
        if (!$classe) {
            return response()->json(['error' => 'Classe non trouvée'], 422);
        }

        // Préparer les données pour l'élève
        $eleveData = [
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'date_naissance' => $validated['date_naissance'],
            'classe_id' => $classe->id,
        ];

        if ($request->hasFile('chemin_document')) {
            $eleveData['chemin_document'] = $request->file('chemin_document')->store('documents');
        }

        $eleve = \App\Models\Eleve::create($eleveData);

       


$identifiant = 'EL' . date('Y') . str_pad($eleve->id, 5, '0', STR_PAD_LEFT);

// Mise à jour de l’élève avec l’identifiant
$eleve->identifiant = $identifiant;
$eleve->save();

// Réponse au frontend avec l’identifiant
return response()->json([
    'message' => 'Élève inscrit avec succès',
    'eleve' => $eleve,
    'identifiant' => $identifiant
], 201);

    }
}