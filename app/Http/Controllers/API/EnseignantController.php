<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enseignants = Enseignant::with('utilisateur')->get();
        return response()->json($enseignants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'specialite' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Création du user lié
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'enseignant',
        ]);
        // Création de l'enseignant
        $enseignant = Enseignant::create([
            'utilisateur_id' => $user->id,
            'specialite' => $request->specialite,
        ]);
        return response()->json($enseignant->load('utilisateur'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $enseignant = Enseignant::with('utilisateur')->find($id);
        if (!$enseignant) {
            return response()->json(['message' => 'Enseignant non trouvé'], 404);
        }
        return response()->json($enseignant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $enseignant = Enseignant::find($id);
        if (!$enseignant) {
            return response()->json(['message' => 'Enseignant non trouvé'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . $enseignant->utilisateur_id,
            'password' => 'nullable|string|min:6',
            'specialite' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Mise à jour du user lié
        $user = $enseignant->utilisateur;
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->filled('password')) $user->password = bcrypt($request->password);
        $user->save();
        // Mise à jour de l'enseignant
        if ($request->has('specialite')) $enseignant->specialite = $request->specialite;
        $enseignant->save();
        return response()->json($enseignant->load('utilisateur'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $enseignant = Enseignant::find($id);
        if (!$enseignant) {
            return response()->json(['message' => 'Enseignant non trouvé'], 404);
        }
        // Supprimer aussi le user lié
        $enseignant->utilisateur()->delete();
        $enseignant->delete();
        return response()->json(['message' => 'Enseignant supprimé']);
    }
}
