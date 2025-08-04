<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClasseController extends Controller
{
    /**
     * Afficher la liste des classes
     */
    public function index(): JsonResponse
    {
        $classes = Classe::all();
        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Stocker une nouvelle classe
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => 'required|string|in:6ème,5ème,4ème,3ème,2nde,1ère,Terminale',
            'annee_scolaire' => 'required|string|max:255',
            'effectif' => 'nullable|integer|min:0|max:50',
        ]);

        $classe = Classe::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Classe créée avec succès',
            'data' => $classe
        ], 201);
    }

    /**
     * Afficher une classe spécifique
     */
    public function show(Classe $classe): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $classe
        ]);
    }

    /**
     * Mettre à jour une classe
     */
    public function update(Request $request, Classe $classe): JsonResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => 'required|string|in:6ème,5ème,4ème,3ème,2nde,1ère,Terminale',
            'annee_scolaire' => 'required|string|max:255',
            'effectif' => 'nullable|integer|min:0|max:50',
        ]);

        $classe->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Classe mise à jour avec succès',
            'data' => $classe
        ]);
    }

    /**
     * Supprimer une classe
     */
    public function destroy(Classe $classe): JsonResponse
    {
        $classe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Classe supprimée avec succès'
        ]);
    }

    /**
     * Obtenir les classes par niveau
     */
    public function getByNiveau(string $niveau): JsonResponse
    {
        $classes = Classe::where('niveau', $niveau)->get();
        
        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Obtenir les classes par année scolaire
     */
    public function getByAnnee(string $annee): JsonResponse
    {
        $classes = Classe::where('annee_scolaire', $annee)->get();
        
        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Obtenir les statistiques des classes
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_classes' => Classe::count(),
            'total_eleves' => Classe::sum('effectif'),
            'par_niveau' => Classe::selectRaw('niveau, COUNT(*) as count')
                ->groupBy('niveau')
                ->get(),
            'par_annee' => Classe::selectRaw('annee_scolaire, COUNT(*) as count')
                ->groupBy('annee_scolaire')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
