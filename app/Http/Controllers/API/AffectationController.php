<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Affectation;
use Illuminate\Support\Facades\Validator;

class AffectationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $affectations = Affectation::with('enseignant.utilisateur', 'matiere', 'classe')->get();
        return response()->json($affectations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enseignant_id' => 'required|exists:enseignants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'classe_id' => 'required|exists:classes,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $affectation = Affectation::create($request->only(['enseignant_id', 'matiere_id', 'classe_id']));
        return response()->json($affectation->load('enseignant.utilisateur', 'matiere', 'classe'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $affectation = Affectation::with('enseignant.utilisateur', 'matiere', 'classe')->find($id);
        if (!$affectation) {
            return response()->json(['message' => 'Affectation non trouvée'], 404);
        }
        return response()->json($affectation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $affectation = Affectation::find($id);
        if (!$affectation) {
            return response()->json(['message' => 'Affectation non trouvée'], 404);
        }
        $validator = Validator::make($request->all(), [
            'enseignant_id' => 'sometimes|required|exists:enseignants,id',
            'matiere_id' => 'sometimes|required|exists:matieres,id',
            'classe_id' => 'sometimes|required|exists:classes,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $affectation->update($request->only(['enseignant_id', 'matiere_id', 'classe_id']));
        return response()->json($affectation->load('enseignant.utilisateur', 'matiere', 'classe'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $affectation = Affectation::find($id);
        if (!$affectation) {
            return response()->json(['message' => 'Affectation non trouvée'], 404);
        }
        $affectation->delete();
        return response()->json(['message' => 'Affectation supprimée']);
    }
}
