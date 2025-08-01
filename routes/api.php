<?php

use App\Http\Controllers\API\CategorieController;
use App\Http\Controllers\API\ProduitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EnseignantController;
use App\Http\Controllers\API\MatiereController;
use App\Http\Controllers\API\ClasseController;
use App\Http\Controllers\API\EleveController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\AffectationController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::apiResource('categories', CategorieController::class);
Route::apiResource('produits', ProduitController::class);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('enseignants', EnseignantController::class);
Route::apiResource('matieres', MatiereController::class);
Route::apiResource('classes', ClasseController::class);
Route::apiResource('eleves', EleveController::class);
Route::apiResource('notes', NoteController::class);
Route::apiResource('affectations', AffectationController::class);