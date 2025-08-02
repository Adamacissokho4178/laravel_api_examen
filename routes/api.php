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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes publiques
Route::apiResource('enseignants', EnseignantController::class);
Route::apiResource('matieres', MatiereController::class);
Route::get('/matieres/niveau/{niveau}', [MatiereController::class, 'getByNiveau']);

// Routes Notes
Route::apiResource('notes', NoteController::class);
Route::get('/notes/eleve/{eleveId}', [NoteController::class, 'getByEleve']);
Route::get('/notes/matiere/{matiereId}', [NoteController::class, 'getByMatiere']);
Route::get('/notes/periode/{periode}', [NoteController::class, 'getByPeriode']);
Route::get('/notes/moyenne/{eleveId}', [NoteController::class, 'calculerMoyenne']);
Route::get('/notes/moyenne/{eleveId}/{periode}', [NoteController::class, 'calculerMoyenne']);

// Routes Dashboard
Route::get('/dashboard/stats-globales', [DashboardController::class, 'getGlobalStats']);
Route::get('/dashboard/stats-academiques', [DashboardController::class, 'getAcademicStats']);
Route::get('/dashboard/suivi-notes', [DashboardController::class, 'getNotesTracking']);
Route::get('/dashboard/overview', [DashboardController::class, 'getDashboardOverview']);
Route::get('/dashboard/stats-periode/{periode}', [DashboardController::class, 'getStatsByPeriod']);

Route::apiResource('affectations', AffectationController::class);
Route::post('/eleves', [EleveController::class, 'store']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Autres routes protégées ici
});
