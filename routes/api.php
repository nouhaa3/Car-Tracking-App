<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VoitureController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\DocumentVehiculeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\CorbeilleController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes - Car Tracking Application
|--------------------------------------------------------------------------
|
| Organized by authentication level and role-based access control
| - Public routes (no authentication)
| - Authenticated routes (all roles)
| - Role-specific routes (Admin, Agent, Technicien)
|
*/

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Password Reset
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Contact Form (Public)
Route::post('/contact-messages', [ContactMessageController::class, 'store'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Messages (Public)
Route::post('/messages', [MessageController::class, 'store']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (All Roles)
|--------------------------------------------------------------------------
| These routes require authentication but are accessible to all roles
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        $user = $request->user();
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        return response()->json($user);
    });
    
    // Vehicles (Read Only)
    Route::get('/voitures', [VoitureController::class, 'index']);
    Route::get('/voitures/count-total', [VoitureController::class, 'countTotal']);
    Route::get('/voitures/count-by-status', [VoitureController::class, 'countByStatus']);
    Route::get('/voitures/top-expensive', [VoitureController::class, 'getTopExpensive']);
    Route::get('/voitures/by-year', [VoitureController::class, 'getByYear']);
    Route::get('/voitures/availability-rate', [VoitureController::class, 'getAvailabilityRate']);
    Route::get('/voitures/{idVoiture}', [VoitureController::class, 'show']);
    
    // Interventions (Read Only)
    Route::get('/interventions', [InterventionController::class, 'index']);
    Route::get('/interventions/recent-history', [InterventionController::class, 'getRecentHistory']);
    Route::get('/interventions/count-by-month', [InterventionController::class, 'countByMonth']);
    Route::get('/interventions/count-by-type', [InterventionController::class, 'countByType']);
    Route::get('/interventions/{id}', [InterventionController::class, 'show']);
    
    // Documents (Read & Download)
    Route::get('/voitures/{voitureId}/documents', [DocumentVehiculeController::class, 'index']);
    Route::get('/documents-vehicule/{id}/download', [DocumentVehiculeController::class, 'download']);
    Route::get('/documents-vehicule/expiring', [DocumentVehiculeController::class, 'expiring']);
    Route::get('/documents-vehicule/expired', [DocumentVehiculeController::class, 'expired']);
    
    // Alerts (Read Only)
    Route::get('/alertes', [AlerteController::class, 'index']);
    Route::get('/alertes/stats', [AlerteController::class, 'getStats']);
    Route::get('/alertes/{id}', [AlerteController::class, 'show']);
    
    // Maintenance History (Read Only)
    Route::get('/voitures/{voitureId}/historique', [HistoriqueController::class, 'getHistoriqueVehicule']);
    Route::get('/maintenance/total-cost', [MaintenanceController::class, 'getTotalCost']);
    Route::get('/maintenance/cost-by-month', [MaintenanceController::class, 'getCostByMonth']);
    
    // Notifications (All Users)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('/notifications/recent', [NotificationController::class, 'recent']);
    Route::get('/notifications/stats', [NotificationController::class, 'stats']);
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/delete-all-read', [NotificationController::class, 'deleteAllRead']);
    Route::post('/notifications/test', [NotificationController::class, 'sendTest']);
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/{id}/unread', [NotificationController::class, 'markAsUnread']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    
    // Settings (All Users)
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings', [SettingsController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| ADMIN-ONLY ROUTES
|--------------------------------------------------------------------------
| Full system access - User, Role, Vehicle, Intervention, Alert Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    
    // === USER MANAGEMENT ===
    Route::prefix('users')->group(function () {
        Route::get('/count-by-role', [UserController::class, 'countByRole']);
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/{id}/avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('/{id}/avatar', [UserController::class, 'deleteAvatar']);
        
        // User Approval Routes
        Route::post('/{id}/approve', [AuthController::class, 'approveUser']);
        Route::post('/{id}/reject', [AuthController::class, 'rejectUser']);
    });
    
    // === ROLE MANAGEMENT ===
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
    });
    
    // === VEHICLE MANAGEMENT ===
    Route::prefix('voitures')->group(function () {
        Route::get('/export/excel', [VoitureController::class, 'export']);
        Route::post('/import/excel', [VoitureController::class, 'import']);
        Route::get('/import/template', [VoitureController::class, 'downloadTemplate']);
        Route::post('/', [VoitureController::class, 'store']);
        Route::put('/{idVoiture}', [VoitureController::class, 'update']);
        Route::delete('/{idVoiture}', [VoitureController::class, 'destroy']);
        Route::post('/{idVoiture}/image', [VoitureController::class, 'uploadImage']);
        Route::delete('/{idVoiture}/image', [VoitureController::class, 'deleteImage']);
    });
    
    // === INTERVENTION MANAGEMENT ===
    Route::prefix('interventions')->group(function () {
        Route::post('/', [InterventionController::class, 'store']);
        Route::post('/{id}/duplicate', [InterventionController::class, 'duplicate']);
        Route::put('/{id}', [InterventionController::class, 'update']);
        Route::delete('/{id}', [InterventionController::class, 'destroy']);
    });
    
    // === ALERT MANAGEMENT ===
    Route::prefix('alertes')->group(function () {
        Route::post('/generate', [AlerteController::class, 'generateAlerts']);
        Route::post('/generate/{voitureId}', [AlerteController::class, 'generateForVehicle']);
        Route::delete('/cleanup', [AlerteController::class, 'cleanup']);
        Route::post('/', [AlerteController::class, 'store']);
        Route::put('/{id}', [AlerteController::class, 'update']);
        Route::delete('/{id}', [AlerteController::class, 'destroy']);
    });
    
    // === DOCUMENT MANAGEMENT ===
    Route::prefix('documents-vehicule')->group(function () {
        Route::put('/{id}', [DocumentVehiculeController::class, 'update']);
        Route::post('/{id}', [DocumentVehiculeController::class, 'update']); // For form-data
        Route::delete('/{id}', [DocumentVehiculeController::class, 'destroy']);
    });
    
    // === CONTACT MESSAGES ===
    Route::prefix('contact-messages')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index']);
        Route::get('/unread-count', [ContactMessageController::class, 'unreadCount']);
        Route::get('/{id}', [ContactMessageController::class, 'show']);
        Route::patch('/{id}/read', [ContactMessageController::class, 'markAsRead']);
        Route::post('/{id}/reply', [ContactMessageController::class, 'reply']);
        Route::delete('/{id}', [ContactMessageController::class, 'destroy']);
    });
    
    // === TRASH/CORBEILLE ===
    Route::prefix('corbeille')->group(function () {
        Route::get('/voitures', [CorbeilleController::class, 'getVoituresTrashed']);
        Route::get('/interventions', [CorbeilleController::class, 'getInterventionsTrashed']);
        Route::post('/voitures/{id}/restore', [CorbeilleController::class, 'restoreVoiture']);
        Route::post('/interventions/{id}/restore', [CorbeilleController::class, 'restoreIntervention']);
        Route::delete('/voitures/{id}/force', [CorbeilleController::class, 'forceDeleteVoiture']);
        Route::delete('/interventions/{id}/force', [CorbeilleController::class, 'forceDeleteIntervention']);
    });
    
    // === REPORTS (All Types - Admin Only) ===
    Route::prefix('rapports')->group(function () {
        Route::get('/users/{format}', [RapportController::class, 'rapportUsers']);
        Route::get('/financier/{format}', [RapportController::class, 'rapportFinancier']);
        Route::get('/complet/{format}', [RapportController::class, 'rapportComplet']);
        Route::post('/custom/{format}', [RapportController::class, 'rapportCustom']);
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN + AGENT ROUTES
|--------------------------------------------------------------------------
| Document upload permissions
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin.or.agent'])->group(function () {
    // Document Upload
    Route::post('/voitures/{voitureId}/documents', [DocumentVehiculeController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| ADMIN + TECHNICIEN ROUTES
|--------------------------------------------------------------------------
| Alert resolution and limited reports
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin.or.technicien'])->group(function () {
    // Alert Resolution
    Route::patch('/alertes/{id}/resolve', [AlerteController::class, 'resolve']);
    
    // Reports (Limited to Vehicles & Interventions)
    Route::get('/rapports/voitures/{format}', [RapportController::class, 'rapportVoitures']);
    Route::get('/rapports/interventions/{format}', [RapportController::class, 'rapportInterventions']);
});
