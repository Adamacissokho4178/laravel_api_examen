<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\User;
use App\Models\Affectation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get global statistics.
     */
    public function getGlobalStats(): JsonResponse
    {
        try {
            $stats = [
                'total_eleves' => Eleve::count(),
                'total_classes' => Classe::count(),
                'total_enseignants' => Enseignant::count(),
                'total_matieres' => Matiere::count(),
                'total_notes' => Note::count(),
                'total_users' => User::count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistiques globales récupérées avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques globales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get academic statistics by class.
     */
    public function getAcademicStats(): JsonResponse
    {
        try {
            $classes = Classe::with(['eleves.notes.matiere'])->get();
            $academicStats = [];

            foreach ($classes as $classe) {
                $eleves = $classe->eleves;
                $classStats = [
                    'classe_id' => $classe->id,
                    'classe_nom' => $classe->nom,
                    'nombre_eleves' => $eleves->count(),
                    'moyenne_classe' => 0,
                    'notes_saisies' => 0,
                    'matieres_enseignees' => 0,
                ];

                if ($eleves->count() > 0) {
                    $totalMoyennes = 0;
                    $elevesAvecNotes = 0;

                    foreach ($eleves as $eleve) {
                        $notes = $eleve->notes;
                        $classStats['notes_saisies'] += $notes->count();

                        if ($notes->count() > 0) {
                            // Calculer la moyenne de l'élève
                            $totalPoints = 0;
                            $totalCoefficients = 0;

                            foreach ($notes as $note) {
                                $coefficient = $note->coefficient ?? $note->matiere->coefficient ?? 1;
                                $totalPoints += $note->note * $coefficient;
                                $totalCoefficients += $coefficient;
                            }

                            if ($totalCoefficients > 0) {
                                $moyenneEleve = $totalPoints / $totalCoefficients;
                                $totalMoyennes += $moyenneEleve;
                                $elevesAvecNotes++;
                            }
                        }
                    }

                    if ($elevesAvecNotes > 0) {
                        $classStats['moyenne_classe'] = round($totalMoyennes / $elevesAvecNotes, 2);
                    }
                }

                // Compter les matières enseignées dans cette classe
                $matieresClasse = Matiere::whereHas('notes', function ($query) use ($classe) {
                    $query->whereHas('eleve', function ($q) use ($classe) {
                        $q->where('classe_id', $classe->id);
                    });
                })->distinct()->count();

                $classStats['matieres_enseignees'] = $matieresClasse;
                $academicStats[] = $classStats;
            }

            return response()->json([
                'success' => true,
                'data' => $academicStats,
                'message' => 'Statistiques académiques récupérées avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques académiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notes tracking statistics.
     */
    public function getNotesTracking(): JsonResponse
    {
        try {
            $notesTracking = [
                'total_notes_saisies' => Note::count(),
                'notes_par_periode' => Note::select('periode', DB::raw('count(*) as total'))
                    ->groupBy('periode')
                    ->get(),
                'notes_par_matiere' => Note::select('matiere_id', DB::raw('count(*) as total'))
                    ->with('matiere:id,nom')
                    ->groupBy('matiere_id')
                    ->get(),
                'notes_par_enseignant' => Note::select('enseignant_id', DB::raw('count(*) as total'))
                    ->with('enseignant.utilisateur:id,name')
                    ->groupBy('enseignant_id')
                    ->get(),
                'dernieres_notes' => Note::with(['eleve', 'matiere', 'enseignant.utilisateur'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $notesTracking,
                'message' => 'Suivi des notes récupéré avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du suivi des notes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard overview data.
     */
    public function getDashboardOverview(): JsonResponse
    {
        try {
            $overview = [
                'statistiques_globales' => [
                    'total_eleves' => Eleve::count(),
                    'total_classes' => Classe::count(),
                    'total_enseignants' => Enseignant::count(),
                    'total_matieres' => Matiere::count(),
                    'total_notes' => Note::count(),
                ],
                'statistiques_par_role' => [
                    'admins' => User::where('role', 'admin')->count(),
                    'enseignants' => User::where('role', 'enseignant')->count(),
                    'eleves_parents' => User::where('role', 'eleve_parent')->count(),
                ],
                'activite_recente' => [
                    'notes_ce_mois' => Note::whereMonth('created_at', now()->month)->count(),
                    'nouveaux_eleves' => Eleve::whereMonth('created_at', now()->month)->count(),
                    'nouveaux_enseignants' => Enseignant::whereMonth('created_at', now()->month)->count(),
                ],
                'performances_classes' => $this->getClassesPerformance(),
            ];

            return response()->json([
                'success' => true,
                'data' => $overview,
                'message' => 'Vue d\'ensemble du tableau de bord récupérée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la vue d\'ensemble',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get classes performance ranking.
     */
    private function getClassesPerformance(): array
    {
        $classes = Classe::with(['eleves.notes.matiere'])->get();
        $performance = [];

        foreach ($classes as $classe) {
            $eleves = $classe->eleves;
            $totalMoyennes = 0;
            $elevesAvecNotes = 0;

            foreach ($eleves as $eleve) {
                $notes = $eleve->notes;
                if ($notes->count() > 0) {
                    $totalPoints = 0;
                    $totalCoefficients = 0;

                    foreach ($notes as $note) {
                        $coefficient = $note->coefficient ?? $note->matiere->coefficient ?? 1;
                        $totalPoints += $note->note * $coefficient;
                        $totalCoefficients += $coefficient;
                    }

                    if ($totalCoefficients > 0) {
                        $moyenneEleve = $totalPoints / $totalCoefficients;
                        $totalMoyennes += $moyenneEleve;
                        $elevesAvecNotes++;
                    }
                }
            }

            $moyenneClasse = $elevesAvecNotes > 0 ? round($totalMoyennes / $elevesAvecNotes, 2) : 0;

            $performance[] = [
                'classe_id' => $classe->id,
                'classe_nom' => $classe->nom,
                'moyenne_classe' => $moyenneClasse,
                'nombre_eleves' => $eleves->count(),
                'eleves_avec_notes' => $elevesAvecNotes,
            ];
        }

        // Trier par moyenne décroissante
        usort($performance, function ($a, $b) {
            return $b['moyenne_classe'] <=> $a['moyenne_classe'];
        });

        return $performance;
    }

    /**
     * Get statistics by period.
     */
    public function getStatsByPeriod(string $periode): JsonResponse
    {
        try {
            $stats = [
                'periode' => $periode,
                'notes_periode' => Note::where('periode', $periode)->count(),
                'moyenne_periode' => 0,
                'meilleure_classe' => null,
                'matieres_evaluees' => Note::where('periode', $periode)
                    ->with('matiere:id,nom')
                    ->get()
                    ->groupBy('matiere_id')
                    ->map(function ($notes) {
                        return [
                            'matiere' => $notes->first()->matiere->nom,
                            'nombre_notes' => $notes->count(),
                            'moyenne' => round($notes->avg('note'), 2),
                        ];
                    }),
            ];

            // Calculer la moyenne générale de la période
            $notesPeriode = Note::where('periode', $periode)->get();
            if ($notesPeriode->count() > 0) {
                $stats['moyenne_periode'] = round($notesPeriode->avg('note'), 2);
            }

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => "Statistiques de la période {$periode} récupérées avec succès"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques par période',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get affectations statistics.
     */
    public function getAffectationsStats(): JsonResponse
    {
        try {
            $stats = [
                'total_affectations' => Affectation::count(),
                'affectations_actives' => Affectation::where('actif', true)->count(),
                'affectations_par_enseignant' => Affectation::with('enseignant.utilisateur')
                    ->select('enseignant_id', DB::raw('count(*) as total'))
                    ->groupBy('enseignant_id')
                    ->get(),
                'affectations_par_classe' => Affectation::with('classe')
                    ->select('classe_id', DB::raw('count(*) as total'))
                    ->groupBy('classe_id')
                    ->get(),
                'affectations_par_matiere' => Affectation::with('matiere')
                    ->select('matiere_id', DB::raw('count(*) as total'))
                    ->groupBy('matiere_id')
                    ->get(),
                'dernieres_affectations' => Affectation::with(['enseignant.utilisateur', 'matiere', 'classe'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistiques des affectations récupérées avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques des affectations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teacher-specific statistics.
     */
    public function getTeacherStats(int $enseignantId): JsonResponse
    {
        try {
            $enseignant = Enseignant::with('utilisateur')->find($enseignantId);
            if (!$enseignant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enseignant non trouvé'
                ], 404);
            }

            $affectations = Affectation::where('enseignant_id', $enseignantId)
                ->with(['matiere', 'classe'])
                ->get();

            $notes = Note::where('enseignant_id', $enseignantId)
                ->with(['eleve.classe', 'matiere'])
                ->get();

            $stats = [
                'enseignant' => [
                    'id' => $enseignant->id,
                    'nom' => $enseignant->utilisateur->name,
                    'specialite' => $enseignant->specialite,
                ],
                'affectations' => [
                    'total' => $affectations->count(),
                    'classes_enseignees' => $affectations->pluck('classe.nom')->unique()->values(),
                    'matieres_enseignees' => $affectations->pluck('matiere.nom')->unique()->values(),
                ],
                'notes' => [
                    'total_saisies' => $notes->count(),
                    'moyenne_generale' => $notes->count() > 0 ? round($notes->avg('note'), 2) : 0,
                    'notes_par_periode' => $notes->groupBy('periode')->map(function ($notesPeriode) {
                        return [
                            'nombre' => $notesPeriode->count(),
                            'moyenne' => round($notesPeriode->avg('note'), 2),
                        ];
                    }),
                    'notes_par_matiere' => $notes->groupBy('matiere.nom')->map(function ($notesMatiere) {
                        return [
                            'nombre' => $notesMatiere->count(),
                            'moyenne' => round($notesMatiere->avg('note'), 2),
                        ];
                    }),
                ],
                'dernieres_activites' => [
                    'dernieres_notes' => $notes->sortByDesc('created_at')->take(5),
                    'dernieres_affectations' => $affectations->sortByDesc('created_at')->take(5),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistiques de l\'enseignant récupérées avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques de l\'enseignant',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
