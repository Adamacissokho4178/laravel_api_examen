<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Matiere;
use Illuminate\Support\Facades\Validator;

class MatiereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $matieres = Matiere::all();
        return response()->json($matieres);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'niveau' => 'required|string',
            'coefficient' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $matiere = Matiere::create($request->only(['nom', 'niveau', 'coefficient']));
        return response()->json($matiere, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $matiere = Matiere::find($id);
        if (!$matiere) {
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }
        return response()->json($matiere);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $matiere = Matiere::find($id);
        if (!$matiere) {
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|required|string',
            'niveau' => 'sometimes|required|string',
            'coefficient' => 'sometimes|required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $matiere->update($request->only(['nom', 'niveau', 'coefficient']));
        return response()->json($matiere);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $matiere = Matiere::find($id);
        if (!$matiere) {
            return response()->json(['message' => 'Matière non trouvée'], 404);
        }
        $matiere->delete();
        return response()->json(['message' => 'Matière supprimée']);
    }
}
