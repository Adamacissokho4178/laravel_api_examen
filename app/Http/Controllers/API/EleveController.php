<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eleve;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class EleveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eleves = Eleve::with('classe', 'utilisateur')->get();
        return response()->json($eleves);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'date_naissance' => 'required|date',
            'classe_id' => 'nullable|exists:classes,id',
            'chemin_document' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Création du user lié
        $user = User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'eleve_parent',
        ]);
        // Création de l'élève
        $eleve = Eleve::create([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'date_naissance' => $request->date_naissance,
            'classe_id' => $request->classe_id,
            'chemin_document' => $request->chemin_document,
            'utilisateur_id' => $user->id,
        ]);
        return response()->json($eleve->load('classe', 'utilisateur'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $eleve = Eleve::with('classe', 'utilisateur')->find($id);
        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }
        return response()->json($eleve);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $eleve = Eleve::find($id);
        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }
        $validator = Validator::make($request->all(), [
            'prenom' => 'sometimes|required|string',
            'nom' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . $eleve->utilisateur_id,
            'date_naissance' => 'sometimes|required|date',
            'classe_id' => 'nullable|exists:classes,id',
            'chemin_document' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Mise à jour du user lié
        $user = $eleve->utilisateur;
        if ($request->has('prenom') || $request->has('nom')) {
            $user->name = ($request->prenom ?? $eleve->prenom) . ' ' . ($request->nom ?? $eleve->nom);
        }
        if ($request->has('email')) $user->email = $request->email;
        if ($request->filled('password')) $user->password = bcrypt($request->password);
        $user->save();
        // Mise à jour de l'élève
        $eleve->update($request->only(['prenom', 'nom', 'email', 'date_naissance', 'classe_id', 'chemin_document']));
        return response()->json($eleve->load('classe', 'utilisateur'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $eleve = Eleve::find($id);
        if (!$eleve) {
            return response()->json(['message' => 'Élève non trouvé'], 404);
        }
        // Supprimer aussi le user lié
        $eleve->utilisateur()->delete();
        $eleve->delete();
        return response()->json(['message' => 'Élève supprimé']);
    }
}
