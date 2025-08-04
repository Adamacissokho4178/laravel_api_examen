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
        'specialite' => 'required|string|max:100'
    ]);

    // Créer l'enseignant dans la base de données
    $enseignant = Enseignant::create($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Enseignant créé avec succès',
        'enseignant' => $enseignant
    ], 201);
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

    // Trouver et mettre à jour l'enseignant
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

// Routes pour les classes (utilisant la base de données)
Route::get('/classes', function () {
    return Classe::all();
});

Route::get('/classes/{id}', function ($id) {
    $classe = Classe::find($id);
    if (!$classe) {
        return response()->json(['error' => 'Classe non trouvée'], 404);
    }
    return $classe;
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

// Routes protégées (avec authentification Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Informations utilisateur
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Routes pour les enseignants (commentées temporairement)
    // Route::apiResource('enseignants', EnseignantController::class);
    
    // Routes pour les matières (commentées temporairement)
    // Route::apiResource('matieres', MatiereController::class);
    
    // Routes pour les notes
    Route::apiResource('notes', NoteController::class);
    
    // Routes pour les classes (à créer)
    Route::apiResource('classes', \App\Http\Controllers\ClasseController::class);
    
    // Routes pour les élèves (à créer)
    Route::apiResource('eleves', \App\Http\Controllers\EleveController::class);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
