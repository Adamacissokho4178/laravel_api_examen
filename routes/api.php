<?php

use App\Http\Controllers\API\CategorieController;
use App\Http\Controllers\API\ProduitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EnseignantController;
use App\Http\Controllers\API\MatiereController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\AffectationController;
use App\Http\Controllers\API\DashboardController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::apiResource('categories', CategorieController::class);
Route::apiResource('produits', ProduitController::class);
// Routes d'authentification (publiques)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Route pour obtenir les infos de l'utilisateur connecté
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes pour Admin seulement
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('enseignants', EnseignantController::class);
        Route::get('/enseignants/specialite/{specialite}', [EnseignantController::class, 'searchBySpecialite']);
        
        Route::apiResource('matieres', MatiereController::class);
        Route::get('/matieres/niveau/{niveau}', [MatiereController::class, 'getByNiveau']);
    });

    // Routes pour Admin et Enseignant
    Route::middleware('role:admin,enseignant')->group(function () {
        Route::apiResource('notes', NoteController::class);
        Route::get('/notes/eleve/{eleveId}', [NoteController::class, 'getByEleve']);
        Route::get('/notes/matiere/{matiereId}', [NoteController::class, 'getByMatiere']);
        Route::get('/notes/periode/{periode}', [NoteController::class, 'getByPeriode']);
        
        // Routes de calculs et statistiques
        Route::get('/notes/moyenne/{eleveId}', [NoteController::class, 'calculerMoyenne']);
        Route::get('/notes/moyenne/{eleveId}/{periode}', [NoteController::class, 'calculerMoyenne']);
        Route::get('/notes/rang/{eleveId}', [NoteController::class, 'calculerRang']);
        Route::get('/notes/rang/{eleveId}/{periode}', [NoteController::class, 'calculerRang']);
        Route::get('/notes/statistiques', [NoteController::class, 'getStatistiques']);
        Route::get('/notes/statistiques/{periode}', [NoteController::class, 'getStatistiques']);
    });

    // Routes Dashboard (tous les rôles)
    Route::get('/dashboard/stats-globales', [DashboardController::class, 'getGlobalStats']);
    Route::get('/dashboard/stats-academiques', [DashboardController::class, 'getAcademicStats']);
    Route::get('/dashboard/suivi-notes', [DashboardController::class, 'getNotesTracking']);
    Route::get('/dashboard/overview', [DashboardController::class, 'getDashboardOverview']);
    Route::get('/dashboard/stats-periode/{periode}', [DashboardController::class, 'getStatsByPeriod']);
    Route::get('/dashboard/affectations-stats', [DashboardController::class, 'getAffectationsStats']);
    Route::get('/dashboard/teacher-stats/{enseignantId}', [DashboardController::class, 'getTeacherStats']);
});

// Routes publiques (sans authentification)
Route::apiResource('affectations', AffectationController::class);
Route::post('/eleves', [EleveController::class, 'store']);
