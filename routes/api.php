<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\NoteController;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Models\Affectation;
use App\Models\Note;
use App\Models\Eleve;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques (sans authentification)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Route temporaire pour éviter les erreurs 404 (à supprimer plus tard)
Route::get('/produits', function () {
    return response()->json(['message' => 'Route temporaire - à supprimer']);
});

// Routes pour les matières (utilisant la base de données)
Route::get('/matieres', function () {
    return Matiere::all();
});

Route::get('/matieres/{id}', function ($id) {
    $matiere = Matiere::find($id);
    if (!$matiere) {
        return response()->json(['error' => 'Matière non trouvée'], 404);
    }
    return $matiere;
});

Route::post('/matieres', function (Request $request) {
    // Validation des données
    $request->validate([
        'nom' => 'required|string|max:100',
        'niveau' => 'required|string',
        'coefficient' => 'required|numeric|min:0.1|max:10',
        'description' => 'nullable|string|max:1000'
    ]);

    // Créer la matière dans la base de données
    $matiere = Matiere::create($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Matière créée avec succès',
        'matiere' => $matiere
    ], 201);
});

Route::put('/matieres/{id}', function (Request $request, $id) {
    // Validation des données
    $request->validate([
        'nom' => 'required|string|max:100',
        'niveau' => 'required|string',
        'coefficient' => 'required|numeric|min:0.1|max:10',
        'description' => 'nullable|string|max:1000'
    ]);

    // Trouver et mettre à jour la matière
    $matiere = Matiere::find($id);
    if (!$matiere) {
        return response()->json(['error' => 'Matière non trouvée'], 404);
    }

    $matiere->update($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Matière modifiée avec succès',
        'matiere' => $matiere
    ]);
});

Route::delete('/matieres/{id}', function ($id) {
    $matiere = Matiere::find($id);
    if (!$matiere) {
        return response()->json(['error' => 'Matière non trouvée'], 404);
    }

    $matiere->delete();

    return response()->json([
        'success' => true,
        'message' => 'Matière supprimée avec succès',
        'id' => $id
    ]);
});

// Routes pour les enseignants (utilisant la base de données)
Route::get('/enseignants', function () {
    return Enseignant::all();
});

Route::get('/enseignants/{id}', function ($id) {
    $enseignant = Enseignant::find($id);
    if (!$enseignant) {
        return response()->json(['error' => 'Enseignant non trouvé'], 404);
    }
    return $enseignant;
});

Route::post('/enseignants', function (Request $request) {
    // Validation des données
    $request->validate([
        'nom' => 'required|string|max:50',
        'prenom' => 'required|string|max:50',
        'email' => 'required|email|unique:enseignants,email',
        'telephone' => 'nullable|string|max:20',
        'specialite' => 'required|string|max:100',
        'password' => 'required|string|min:6'
    ]);

    try {
        // Créer un compte utilisateur
        $user = \App\Models\User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'enseignant'
        ]);

        // Créer l'enseignant avec la référence vers l'utilisateur
        $enseignant = Enseignant::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'specialite' => $request->specialite,
            'utilisateur_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enseignant créé avec succès',
            'enseignant' => $enseignant
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création de l\'enseignant: ' . $e->getMessage()
        ], 500);
    }
});

Route::put('/enseignants/{id}', function (Request $request, $id) {
    // Validation des données
    $request->validate([
        'nom' => 'required|string|max:50',
        'prenom' => 'required|string|max:50',
        'email' => 'required|email|unique:enseignants,email,' . $id,
        'telephone' => 'nullable|string|max:20',
        'specialite' => 'required|string|max:100'
    ]);

    
    $enseignant = Enseignant::find($id);
    if (!$enseignant) {
        return response()->json(['error' => 'Enseignant non trouvé'], 404);
    }

    $enseignant->update($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Enseignant modifié avec succès',
        'enseignant' => $enseignant
    ]);
});

Route::delete('/enseignants/{id}', function ($id) {
    $enseignant = Enseignant::find($id);
    if (!$enseignant) {
        return response()->json(['error' => 'Enseignant non trouvé'], 404);
    }

    $enseignant->delete();

    return response()->json([
        'success' => true,
        'message' => 'Enseignant supprimé avec succès',
        'id' => $id
    ]);
});

// Routes pour les affectations (utilisant la base de données)
Route::get('/affectations', function () {
    return Affectation::with(['enseignant', 'matiere', 'classe'])->get();
});

Route::get('/affectations/{id}', function ($id) {
    $affectation = Affectation::with(['enseignant', 'matiere', 'classe'])->find($id);
    if (!$affectation) {
        return response()->json(['error' => 'Affectation non trouvée'], 404);
    }
    return $affectation;
});

Route::post('/affectations', function (Request $request) {
    // Validation des données
    $request->validate([
        'enseignant_id' => 'required|exists:enseignants,id',
        'matiere_id' => 'required|exists:matieres,id',
        'classe_id' => 'required|exists:classes,id'
    ]);

    // Vérifier si l'affectation existe déjà
    $existingAffectation = Affectation::where([
        'enseignant_id' => $request->enseignant_id,
        'matiere_id' => $request->matiere_id,
        'classe_id' => $request->classe_id
    ])->first();

    if ($existingAffectation) {
        return response()->json([
            'error' => 'Cette affectation existe déjà'
        ], 400);
    }

    // Créer l'affectation dans la base de données
    $affectation = Affectation::create($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Affectation créée avec succès',
        'affectation' => $affectation->load(['enseignant', 'matiere', 'classe'])
    ], 201);
});

Route::put('/affectations/{id}', function (Request $request, $id) {
    // Validation des données
    $request->validate([
        'enseignant_id' => 'required|exists:enseignants,id',
        'matiere_id' => 'required|exists:matieres,id',
        'classe_id' => 'required|exists:classes,id'
    ]);

    // Trouver et mettre à jour l'affectation
    $affectation = Affectation::find($id);
    if (!$affectation) {
        return response()->json(['error' => 'Affectation non trouvée'], 404);
    }

    $affectation->update($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Affectation modifiée avec succès',
        'affectation' => $affectation->load(['enseignant', 'matiere', 'classe'])
    ]);
});

Route::delete('/affectations/{id}', function ($id) {
    $affectation = Affectation::find($id);
    if (!$affectation) {
        return response()->json(['error' => 'Affectation non trouvée'], 404);
    }

    $affectation->delete();

    return response()->json([
        'success' => true,
        'message' => 'Affectation supprimée avec succès',
        'id' => $id
    ]);
});

// Routes pour les classes (utilisant la base de données)
Route::get('/classes', function () {
    try {
        $classes = Classe::all();
        return response()->json($classes);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/classes/{id}', function ($id) {
    $classe = Classe::find($id);
    if (!$classe) {
        return response()->json(['error' => 'Classe non trouvée'], 404);
    }
    return $classe;
});

Route::post('/classes', function (Request $request) {
    // Validation des données
    $request->validate([
        'nom' => 'required|string|max:100',
        'niveau' => 'required|string',
        'capacite' => 'required|integer|min:1|max:50'
    ]);

    // Créer la classe dans la base de données
    $classe = Classe::create($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Classe créée avec succès',
        'classe' => $classe
    ], 201);
});

Route::put('/classes/{id}', function (Request $request, $id) {
    // Validation des données
    $request->validate([
        'nom' => 'required|string|max:100',
        'niveau' => 'required|string',
        'capacite' => 'required|integer|min:1|max:50'
    ]);

    // Trouver et mettre à jour la classe
    $classe = Classe::find($id);
    if (!$classe) {
        return response()->json(['error' => 'Classe non trouvée'], 404);
    }

    $classe->update($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Classe modifiée avec succès',
        'classe' => $classe
    ]);
});

Route::delete('/classes/{id}', function ($id) {
    $classe = Classe::find($id);
    if (!$classe) {
        return response()->json(['error' => 'Classe non trouvée'], 404);
    }

    $classe->delete();

    return response()->json([
        'success' => true,
        'message' => 'Classe supprimée avec succès',
        'id' => $id
    ]);
});

// Routes pour le suivi des notes détaillé
Route::get('/suivi-notes', function (Request $request) {
    try {
        // Récupérer toutes les affectations avec leurs relations
        $affectations = \App\Models\Affectation::with(['enseignant', 'matiere', 'classe'])->get();
        
        $result = [];
        
        foreach ($affectations as $affectation) {
            // Compter les élèves dans cette classe
            $elevesAttendus = \App\Models\Eleve::where('classe_id', $affectation->classe_id)->count();
            
            // Compter les notes pour cette affectation (via matiere_id et enseignant_id)
            $notesSaisies = \App\Models\Note::where('matiere_id', $affectation->matiere_id)
                                           ->where('enseignant_id', $affectation->enseignant_id)
                                           ->count();
            
            $derniereSaisie = \App\Models\Note::where('matiere_id', $affectation->matiere_id)
                                             ->where('enseignant_id', $affectation->enseignant_id)
                                             ->max('created_at');
            
           
            $pourcentage = $elevesAttendus > 0 ? round(($notesSaisies / $elevesAttendus) * 100) : 0;
            
          
            if ($pourcentage == 100) {
                $statut = 'Terminé';
            } elseif ($pourcentage > 0) {
                $statut = 'Incomplet';
            } else {
                $statut = 'Non commencé';
            }
            
            $result[] = [
                'classe' => $affectation->classe->nom ?? 'N/A',
                'matiere' => $affectation->matiere->nom ?? 'N/A',
                'enseignant' => ($affectation->enseignant->prenom ?? '') . ' ' . ($affectation->enseignant->nom ?? ''),
                'trimestre' => 'T1',
                'eleves_attendus' => $elevesAttendus,
                'notes_saisies' => $notesSaisies,
                'pourcentage_avancement' => $pourcentage,
                'derniere_saisie' => $derniereSaisie ? \Carbon\Carbon::parse($derniereSaisie)->format('d/m/Y H:i') : '-',
                'statut' => $statut
            ];
        }
        
        return response()->json($result);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Route pour les détails des notes
Route::get('/notes-details', function (Request $request) {
    $query = \App\Models\Note::with(['eleve', 'matiere', 'classe', 'enseignant']);

    // Filtres
    if ($request->has('classe') && $request->classe) {
        $query->whereHas('classe', function($q) use ($request) {
            $q->where('nom', $request->classe);
        });
    }

    if ($request->has('matiere') && $request->matiere) {
        $query->whereHas('matiere', function($q) use ($request) {
            $q->where('nom', $request->matiere);
        });
    }

    if ($request->has('periode') && $request->periode) {
        $query->where('periode', $request->periode);
    }

    if ($request->has('enseignant') && $request->enseignant) {
        $query->whereHas('enseignant', function($q) use ($request) {
            $q->where('nom', 'LIKE', '%' . $request->enseignant . '%')
              ->orWhere('prenom', 'LIKE', '%' . $request->enseignant . '%');
        });
    }

    $notes = $query->get();

    $result = $notes->map(function($note) {
        return [
            'id' => $note->id,
            'eleve' => [
                'id' => $note->eleve->id ?? 0,
                'nom' => $note->eleve->nom ?? 'N/A',
                'prenom' => $note->eleve->prenom ?? 'N/A',
                'matricule' => $note->eleve->matricule ?? 'N/A'
            ],
            'matiere' => [
                'id' => $note->matiere->id ?? 0,
                'nom' => $note->matiere->nom ?? 'N/A',
                'coefficient' => $note->matiere->coefficient ?? 1
            ],
            'classe' => [
                'id' => $note->classe->id ?? 0,
                'nom' => $note->classe->nom ?? 'N/A'
            ],
            'enseignant' => $note->enseignant ? [
                'id' => $note->enseignant->id ?? 0,
                'nom' => $note->enseignant->nom ?? 'N/A',
                'prenom' => $note->enseignant->prenom ?? 'N/A'
            ] : null,
            'note' => $note->note,
            'appreciation' => $note->appreciation,
            'periode' => $note->periode,
            'date_evaluation' => $note->created_at ? \Carbon\Carbon::parse($note->created_at)->format('d/m/Y H:i') : null,
            'created_at' => $note->created_at,
            'updated_at' => $note->updated_at
        ];
    });

    return response()->json($result);
});

// Route pour les statistiques des notes
Route::get('/statistiques-notes', function () {
    $totalNotes = \App\Models\Note::count();
    $moyenneGenerale = \App\Models\Note::avg('note') ?? 0;
    
    $notesParNiveau = [
        'excellentes' => \App\Models\Note::where('note', '>=', 16)->count(),
        'bonnes' => \App\Models\Note::whereBetween('note', [14, 15.99])->count(),
        'moyennes' => \App\Models\Note::whereBetween('note', [12, 13.99])->count(),
        'passables' => \App\Models\Note::whereBetween('note', [10, 11.99])->count(),
        'insuffisantes' => \App\Models\Note::where('note', '<', 10)->count()
    ];

    return response()->json([
        'total_notes' => $totalNotes,
        'moyenne_generale' => round($moyenneGenerale, 2),
        'notes_excellentes' => $notesParNiveau['excellentes'],
        'notes_bonnes' => $notesParNiveau['bonnes'],
        'notes_moyennes' => $notesParNiveau['moyennes'],
        'notes_passables' => $notesParNiveau['passables'],
        'notes_insuffisantes' => $notesParNiveau['insuffisantes']
    ]);
});

// Route pour les statistiques du dashboard
Route::get('/dashboard-stats', function () {
    try {
        $stats = [
            'notes' => Note::count(),
            'matieres' => Matiere::count(), // Nombre total de matières
            'classes' => Classe::count(),
            'affectations' => Affectation::count(),
            'suiviNotes' => Affectation::count() // Nombre d'affectations = nombre de suivi
        ];
        
        return response()->json($stats);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Route pour récupérer les élèves d'une classe
Route::get('/eleves-classe/{classe_id}', function ($classe_id) {
    try {
        $eleves = Eleve::where('classe_id', $classe_id)
                      ->select('id', 'nom', 'prenom', 'matricule')
                      ->orderBy('nom')
                      ->orderBy('prenom')
                      ->get();
        
        return response()->json($eleves);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Route pour récupérer les notes existantes d'un enseignant
Route::get('/notes-existantes', function (Request $request) {
    try {
        $notes = Note::where([
            'matiere_id' => $request->matiere_id,
            'periode' => $request->periode,
            'enseignant_id' => $request->enseignant_id
        ])->select('id', 'eleve_id', 'note', 'appreciation')
          ->get();
        
        return response()->json($notes);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Route pour récupérer les données des listes déroulantes
Route::get('/dropdown-data', function () {
    try {
        $data = [
            'enseignants' => Enseignant::select('id', 'nom', 'prenom')->get(),
            'matieres' => Matiere::select('id', 'nom')->get(),
            'classes' => Classe::select('id', 'nom')->get()
        ];
        
        return response()->json($data);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
 Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
// Routes protégées (avec authentification Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Informations utilisateur
   

    // Routes pour les enseignants (commentées temporairement)
    // Route::apiResource('enseignants', EnseignantController::class);
    
    // Routes pour les matières (commentées temporairement)
    // Route::apiResource('matieres', MatiereController::class);
    
    // Routes pour les notes
    Route::apiResource('notes', NoteController::class);
    
    // Routes pour les classes (à créer)
    Route::apiResource('classes', \App\Http\Controllers\ClasseController::class);
    
    // Routes pour les élèves (à créer)
    
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('eleves', \App\Http\Controllers\EleveController::class);
Route::get('/eleves/{id}/document', [\App\Http\Controllers\EleveController::class, 'downloadDocument']);

// Routes pour les parents
Route::apiResource('parents', \App\Http\Controllers\ParentController::class);
 Route::get('/parent/mes-enfants', [\App\Http\Controllers\ParentController::class, 'mesEnfants']);
    Route::get('/parent/enfant/{eleveId}/bulletin', [\App\Http\Controllers\ParentController::class, 'bulletinEnfant']);
    Route::get('/parent/enfant/{eleveId}/bulletin/{trimestre}/telecharger', [\App\Http\Controllers\ParentController::class, 'telechargerBulletin']);

// Routes sécurisées pour les parents connectés
Route::middleware('auth:sanctum')->group(function () {
   
});

// Routes pour les calculs de moyennes et statistiques
Route::get('/calculer-moyenne/{eleveId}', function ($eleveId, Request $request) {
    try {
        $periode = $request->get('periode');
        $resultat = \App\Models\Note::calculerMoyenneEleve($eleveId, $periode);
        
        return response()->json([
            'success' => true,
            'data' => $resultat,
            'message' => 'Calcul de moyenne effectué avec succès'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du calcul de la moyenne',
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/statistiques-classe/{classeId}', function ($classeId, Request $request) {
    try {
        $periode = $request->get('periode');
        
        // Récupérer tous les élèves de la classe
        $eleves = \App\Models\Eleve::where('classe_id', $classeId)->get();
        
        $totalMoyenne = 0;
        $nombreEleves = 0;
        $moyennes = [];
        
        foreach ($eleves as $eleve) {
            $resultat = \App\Models\Note::calculerMoyenneEleve($eleve->id, $periode);
            if ($resultat['moyenne'] > 0) {
                $totalMoyenne += $resultat['moyenne'];
                $nombreEleves++;
                $moyennes[] = [
                    'eleve_id' => $eleve->id,
                    'eleve_nom' => $eleve->nom . ' ' . $eleve->prenom,
                    'moyenne' => $resultat['moyenne'],
                    'mention' => $resultat['mention']
                ];
            }
        }
        
        $moyenneClasse = $nombreEleves > 0 ? round($totalMoyenne / $nombreEleves, 2) : 0;
        
        return response()->json([
            'success' => true,
            'data' => [
                'moyenne_classe' => $moyenneClasse,
                'nombre_eleves' => $nombreEleves,
                'periode' => $periode,
                'moyennes_eleves' => $moyennes
            ],
            'message' => 'Statistiques calculées avec succès'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du calcul des statistiques',
            'error' => $e->getMessage()
        ], 500);
    }
});