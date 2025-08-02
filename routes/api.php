<?php

use App\Http\Controllers\API\CategorieController;
use App\Http\Controllers\API\ProduitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\AffectationController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\ClasseController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::apiResource('categories', CategorieController::class);
Route::apiResource('produits', ProduitController::class);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/eleves', [EleveController::class, 'store']);
Route::post('/affectations', [AffectationController::class, 'store']);
Route::get('/enseignants', [EnseignantController::class, 'index']);
Route::get('/matieres', [MatiereController::class, 'index']);
Route::get('/classes', [ClasseController::class, 'index']);
Route::get('/eleves/{id}/document', [EleveController::class, 'downloadDocument']);