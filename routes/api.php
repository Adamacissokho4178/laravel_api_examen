<?php

use App\Http\Controllers\API\CategorieController;
use App\Http\Controllers\API\ProduitController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::apiResource('categories', CategorieController::class);
Route::apiResource('produits', ProduitController::class);