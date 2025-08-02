<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\CategorieController;
use App\Http\Controllers\API\ProduitController;

// Routes web pour l'interface utilisateur (si nécessaire)
Route::get('/', function () {
    return view('welcome');
});