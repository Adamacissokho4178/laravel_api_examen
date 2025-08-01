<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classe;
use Illuminate\Support\Facades\Validator;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classe::all();
        return response()->json($classes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'niveau' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $classe = Classe::create($request->only(['nom', 'niveau']));
        return response()->json($classe, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $classe = Classe::find($id);
        if (!$classe) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }
        return response()->json($classe);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $classe = Classe::find($id);
        if (!$classe) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|required|string',
            'niveau' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $classe->update($request->only(['nom', 'niveau']));
        return response()->json($classe);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $classe = Classe::find($id);
        if (!$classe) {
            return response()->json(['message' => 'Classe non trouvée'], 404);
        }
        $classe->delete();
        return response()->json(['message' => 'Classe supprimée']);
    }
}
