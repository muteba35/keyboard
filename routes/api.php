<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IdentifiantsApiController;

// Route test / affichage rapide
Route::get('/register', [AuthController::class, 'register']); 

// Route pour recevoir les vraies données Flutter
Route::post('/register', [AuthController::class, 'register']); 

// Login (pareil)
Route::get('/login', [AuthController::class, 'login']); 
Route::post('/login', [AuthController::class, 'login']); 

// ✅ Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Infos utilisateur connecté
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD Identifiants
    Route::get('/identifiants', [IdentifiantsApiController::class, 'index']);
    Route::post('/identifiants', [IdentifiantsApiController::class, 'store']);
    Route::get('/identifiants/{id}', [IdentifiantsApiController::class, 'show']);
    Route::put('/identifiants/{id}', [IdentifiantsApiController::class, 'update']);
    Route::delete('/identifiants/{id}', [IdentifiantsApiController::class, 'destroy']);
});
