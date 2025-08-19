<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VoitureController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AlerteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ici, toutes les routes de ton projet sont organisées par rôle
|--------------------------------------------------------------------------
*/

// ✅ Auth routes (communes à tout le monde)
Route::post('/register', [UserController::class, 'store']); // si tu veux laisser l'admin seul, tu peux le mettre ailleurs
Route::post('/login', [UserController::class, 'login']);

// Routes protégées (auth obligatoire)
Route::middleware('auth:sanctum')->group(function () {

    // =====================================================
    // ADMIN
    // =====================================================
    Route::middleware('role:admin')->group(function () {

        // Gestion des utilisateurs
        Route::apiResource('users', UserController::class);

        // Gestion des rôles
        Route::apiResource('roles', RoleController::class);

        // Gestion des voitures
        Route::apiResource('voitures', VoitureController::class);

        // Gestion des interventions
        Route::apiResource('interventions', InterventionController::class);

        // Gestion documents
        Route::apiResource('documents', DocumentController::class);

        // Gestion alertes
        Route::apiResource('alertes', AlerteController::class);
    });


    // =====================================================
    // AGENT
    // =====================================================
    Route::middleware('role:agent')->group(function () {

        // Interventions (CRUD)
        Route::apiResource('interventions', InterventionController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);

        // Documents
        Route::apiResource('documents', DocumentController::class)->only([
            'index', 'store'
        ]);

        // Alertes
        Route::apiResource('alertes', AlerteController::class)->only([
            'index', 'store'
        ]);
    });


    // =====================================================
    // TECHNICIEN
    // =====================================================
    Route::middleware('role:technicien')->group(function () {

        // Consulter alertes
        Route::get('/alertes', [AlerteController::class, 'index']);

        // Marquer une alerte comme traitée
        Route::patch('/alertes/{id}/traitee', [AlerteController::class, 'marquerTraitee']);

        // Dashboard (à toi de créer le controller)
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Statistiques et KPIs']);
        });

        // Exporter rapports
        Route::get('/export-rapports', function () {
            return response()->json(['message' => 'Rapport exporté avec succès']);
        });
    });


    // =====================================================
    // ROUTES COMMUNES (tous les rôles authentifiés)
    // =====================================================
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [UserController::class, 'logout']);
});
