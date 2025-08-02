<?php
namespace App\Http\Controllers;

use App\Models\Affectation;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'enseignant_id' => 'required|exists:enseignants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'classe_nom' => 'required|exists:classes,nom',
        ]);

        // Trouver la classe par son nom
        $classe = \App\Models\Classe::where('nom', $validated['classe_nom'])->first();

        $affectation = Affectation::create([
            'enseignant_id' => $validated['enseignant_id'],
            'matiere_id' => $validated['matiere_id'],
            'classe_id' => $classe->id,
        ]);

        return response()->json($affectation, 201);
    }
}