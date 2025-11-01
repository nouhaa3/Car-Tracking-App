<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VoitureController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ici, toutes les routes de ton projet sont organisées par rôle
|--------------------------------------------------------------------------
*/

use App\Models\User;
use Illuminate\Support\Facades\Hash;


Route::middleware('auth:sanctum')->group(function () {
    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings', [SettingsController::class, 'store']);
    
    // Maintenance stats
    Route::get('/maintenance/total-cost', [MaintenanceController::class, 'getTotalCost']);
    Route::get('/maintenance/cost-by-month', [MaintenanceController::class, 'getCostByMonth']);
    
    // Vehicle stats
    Route::get('/voitures/top-expensive', [VoitureController::class, 'getTopExpensive']);
    Route::get('/voitures/by-year', [VoitureController::class, 'getByYear']);
    Route::get('/voitures/availability-rate', [VoitureController::class, 'getAvailabilityRate']);
    
    // Alerts stats
    Route::get('/alertes/stats', [AlerteController::class, 'getStats']);
    
    // Dashboard stats
    Route::get('/voitures/count-by-status', [VoitureController::class, 'countByStatus']);
    Route::get('/users/count-by-role', [UserController::class, 'countByRole']);
    
    // Alerts management (full CRUD)
    Route::get('/alertes', [AlerteController::class, 'index']);
    Route::post('/alertes', [AlerteController::class, 'store']);
    Route::get('/alertes/{id}', [AlerteController::class, 'show']);
    Route::put('/alertes/{id}', [AlerteController::class, 'update']);
    Route::delete('/alertes/{id}', [AlerteController::class, 'destroy']);
    Route::patch('/alertes/{id}/resolve', [AlerteController::class, 'resolve']);
    Route::post('/alertes/generate', [AlerteController::class, 'generateAlerts']);
    Route::post('/alertes/generate/{voitureId}', [AlerteController::class, 'generateForVehicle']);
    Route::delete('/alertes/cleanup', [AlerteController::class, 'cleanup']);
    
    // Interventions history
    Route::get('/interventions/recent-history', [InterventionController::class, 'getRecentHistory']);
    // Nombre d'interventions du mois
    Route::get('/interventions/count-by-month', [InterventionController::class, 'countByMonth']);

    // User management
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users', [UserController::class, 'store']);
    Route::post('/users/{id}/avatar', [UserController::class, 'uploadAvatar']);

    // Role management
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
});


Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user()->load('role');
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Password reset routes
use App\Http\Controllers\PasswordResetController;
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
Route::get('/profile', function (Request $request) {
        return $request->user();
    });


// Récupérer une voiture par son idVoiture
Route::get('/voitures', [VoitureController::class, 'index']);
Route::get('/voitures/{idVoiture}', [VoitureController::class, 'show']);
Route::put('/voitures/{idVoiture}', [VoitureController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/voitures/{idVoiture}', [VoitureController::class, 'destroy'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/voitures', [VoitureController::class, 'store']);

// Interventions routes (protected with auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/interventions', [InterventionController::class, 'index']);
    Route::post('/interventions', [InterventionController::class, 'store']);
    Route::get('/interventions/{id}', [InterventionController::class, 'show']);
    Route::put('/interventions/{id}', [InterventionController::class, 'update']);
    Route::delete('/interventions/{id}', [InterventionController::class, 'destroy']);
});

use App\Http\Controllers\MessageController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\DocumentVehiculeController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\CorbeilleController;

Route::post('/messages', [MessageController::class, 'store']);

// Rapports routes (protected with auth - admin only)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/rapports/voitures/{format}', [RapportController::class, 'rapportVoitures']);
    Route::get('/rapports/interventions/{format}', [RapportController::class, 'rapportInterventions']);
    Route::get('/rapports/users/{format}', [RapportController::class, 'rapportUsers']);
    Route::get('/rapports/financier/{format}', [RapportController::class, 'rapportFinancier']);
    Route::get('/rapports/complet/{format}', [RapportController::class, 'rapportComplet']);
    Route::post('/rapports/custom/{format}', [RapportController::class, 'rapportCustom']);
    
    // Documents Véhicule routes
    Route::get('/voitures/{voitureId}/documents', [DocumentVehiculeController::class, 'index']);
    Route::post('/voitures/{voitureId}/documents', [DocumentVehiculeController::class, 'store']);
    Route::get('/documents-vehicule/{id}/download', [DocumentVehiculeController::class, 'download']);
    Route::put('/documents-vehicule/{id}', [DocumentVehiculeController::class, 'update']);
    Route::post('/documents-vehicule/{id}', [DocumentVehiculeController::class, 'update']); // For form-data with _method
    Route::delete('/documents-vehicule/{id}', [DocumentVehiculeController::class, 'destroy']);
    Route::get('/documents-vehicule/expiring', [DocumentVehiculeController::class, 'expiring']);
    Route::get('/documents-vehicule/expired', [DocumentVehiculeController::class, 'expired']);
    
    // Historique Véhicule route
    Route::get('/voitures/{voitureId}/historique', [HistoriqueController::class, 'getHistoriqueVehicule']);
    
    // Corbeille routes
    Route::get('/corbeille/voitures', [CorbeilleController::class, 'getVoituresTrashed']);
    Route::get('/corbeille/interventions', [CorbeilleController::class, 'getInterventionsTrashed']);
    Route::post('/corbeille/voitures/{id}/restore', [CorbeilleController::class, 'restoreVoiture']);
    Route::post('/corbeille/interventions/{id}/restore', [CorbeilleController::class, 'restoreIntervention']);
    Route::delete('/corbeille/voitures/{id}/force', [CorbeilleController::class, 'forceDeleteVoiture']);
    Route::delete('/corbeille/interventions/{id}/force', [CorbeilleController::class, 'forceDeleteIntervention']);
    
    // Duplicate intervention
    Route::post('/interventions/{id}/duplicate', [InterventionController::class, 'duplicate']);
});

// Routes protégées (auth obligatoire)
/*Route::middleware('auth:sanctum')->group(function () {

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

        // Dashboard 
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Statistiques et KPIs']);
        });

        // Exporter rapports
        Route::get('/export-rapports', function () {
            return response()->json(['message' => 'Rapport exporté avec succès']);
        });
    });
*/

    // =====================================================
    // ROUTES COMMUNES (tous les rôles authentifiés)
    // =====================================================
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });


