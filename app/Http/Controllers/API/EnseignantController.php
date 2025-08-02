<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnseignantRequest;
use App\Models\Enseignant;
use Illuminate\Http\JsonResponse;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $enseignants = Enseignant::all();
            return response()->json([
                'success' => true,
                'data' => $enseignants,
                'message' => 'Liste des enseignants récupérée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des enseignants',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnseignantRequest $request): JsonResponse
    {
        try {
            $enseignant = Enseignant::create($request->validated());
            
            return response()->json([
                'success' => true,
                'data' => $enseignant,
                'message' => 'Enseignant créé avec succès'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'enseignant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $enseignant = Enseignant::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $enseignant,
                'message' => 'Enseignant trouvé avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Enseignant non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'enseignant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EnseignantRequest $request, string $id): JsonResponse
    {
        try {
            $enseignant = Enseignant::findOrFail($id);
            $enseignant->update($request->validated());
            
            return response()->json([
                'success' => true,
                'data' => $enseignant,
                'message' => 'Enseignant mis à jour avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Enseignant non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'enseignant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $enseignant = Enseignant::findOrFail($id);
            $enseignant->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Enseignant supprimé avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Enseignant non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'enseignant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechercher des enseignants par spécialité
     */
    public function searchBySpecialite(string $specialite): JsonResponse
    {
        try {
            $enseignants = Enseignant::where('specialite', 'like', "%{$specialite}%")->get();
            
            return response()->json([
                'success' => true,
                'data' => $enseignants,
                'message' => 'Recherche effectuée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
