<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteRequest;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $notes = Note::with(['eleve', 'matiere', 'enseignant'])->get();
            return response()->json([
                'success' => true,
                'data' => $notes,
                'message' => 'Liste des notes récupérée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NoteRequest $request): JsonResponse
    {
        try {
            $note = Note::create($request->validated());
            
            // Charger les relations pour la réponse
            $note->load(['eleve', 'matiere', 'enseignant']);
            
            return response()->json([
                'success' => true,
                'data' => $note,
                'message' => 'Note créée avec succès'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la note',
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
            $note = Note::with(['eleve', 'matiere', 'enseignant'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $note,
                'message' => 'Note trouvée avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NoteRequest $request, string $id): JsonResponse
    {
        try {
            $note = Note::findOrFail($id);
            $note->update($request->validated());
            
            // Charger les relations pour la réponse
            $note->load(['eleve', 'matiere', 'enseignant']);
            
            return response()->json([
                'success' => true,
                'data' => $note,
                'message' => 'Note mise à jour avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la note',
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
            $note = Note::findOrFail($id);
            $note->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Note supprimée avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notes by eleve.
     */
    public function getByEleve(string $eleveId): JsonResponse
    {
        try {
            $notes = Note::with(['matiere', 'enseignant'])
                ->where('eleve_id', $eleveId)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $notes,
                'message' => 'Notes de l\'élève récupérées avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notes de l\'élève',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notes by matiere.
     */
    public function getByMatiere(string $matiereId): JsonResponse
    {
        try {
            $notes = Note::with(['eleve', 'enseignant'])
                ->where('matiere_id', $matiereId)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $notes,
                'message' => 'Notes de la matière récupérées avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notes de la matière',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notes by periode.
     */
    public function getByPeriode(string $periode): JsonResponse
    {
        try {
            $notes = Note::with(['eleve', 'matiere', 'enseignant'])
                ->where('periode', $periode)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $notes,
                'message' => "Notes de la période {$periode} récupérées avec succès"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notes par période',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate moyenne for an eleve.
     */
    public function calculerMoyenne(string $eleveId, string $periode = null): JsonResponse
    {
        try {
            $query = Note::where('eleve_id', $eleveId);
            
            if ($periode) {
                $query->where('periode', $periode);
            }
            
            $notes = $query->with('matiere')->get();
            
            if ($notes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune note trouvée pour cet élève'
                ], 404);
            }
            
            // Calculer la moyenne pondérée
            $totalPoints = 0;
            $totalCoefficients = 0;
            
            foreach ($notes as $note) {
                $coefficient = $note->coefficient ?? $note->matiere->coefficient ?? 1;
                $totalPoints += $note->note * $coefficient;
                $totalCoefficients += $coefficient;
            }
            
            $moyenne = $totalCoefficients > 0 ? round($totalPoints / $totalCoefficients, 2) : 0;
            
            // Déterminer la mention
            $mention = $this->determinerMention($moyenne);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'eleve_id' => $eleveId,
                    'periode' => $periode,
                    'moyenne' => $moyenne,
                    'mention' => $mention,
                    'notes' => $notes,
                    'total_points' => $totalPoints,
                    'total_coefficients' => $totalCoefficients
                ],
                'message' => 'Moyenne calculée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul de la moyenne',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determine mention based on moyenne.
     */
    private function determinerMention(float $moyenne): string
    {
        if ($moyenne >= 16) {
            return 'Très Bien';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez Bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }
}
