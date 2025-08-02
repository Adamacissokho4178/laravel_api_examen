<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatiereRequest;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MatiereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $matieres = Matiere::all();
            return response()->json([
                'success' => true,
                'data' => $matieres,
                'message' => 'Liste des matières récupérée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des matières',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MatiereRequest $request): JsonResponse
    {
        try {
            $matiere = Matiere::create($request->validated());
            
            return response()->json([
                'success' => true,
                'data' => $matiere,
                'message' => 'Matière créée avec succès'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la matière',
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
            $matiere = Matiere::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $matiere,
                'message' => 'Matière trouvée avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Matière non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la matière',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MatiereRequest $request, string $id): JsonResponse
    {
        try {
            $matiere = Matiere::findOrFail($id);
            $matiere->update($request->validated());
            
            return response()->json([
                'success' => true,
                'data' => $matiere,
                'message' => 'Matière mise à jour avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Matière non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la matière',
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
            $matiere = Matiere::findOrFail($id);
            $matiere->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Matière supprimée avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Matière non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la matière',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get matières by niveau.
     */
    public function getByNiveau(string $niveau): JsonResponse
    {
        try {
            $matieres = Matiere::where('niveau', $niveau)->get();
            
            return response()->json([
                'success' => true,
                'data' => $matieres,
                'message' => "Matières du niveau {$niveau} récupérées avec succès"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des matières par niveau',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
