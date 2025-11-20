<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Voiture;
use App\Services\AlerteService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlerteController extends Controller
{
    protected $alerteService;
    protected $notificationService;

    public function __construct(AlerteService $alerteService, NotificationService $notificationService)
    {
        $this->alerteService = $alerteService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get all alerts with filtering
     */
    public function index(Request $request)
    {
        $query = Alerte::with(['voiture']);

        // Filter by status
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        // Filter by vehicle
        if ($request->has('voiture_id')) {
            $query->where('voiture_id', $request->voiture_id);
        }

        // Filter by priority (calculated dynamically)
        $alerts = $query->orderBy('dateEchance', 'asc')->get();

        // Add priority to each alert
        $alerts = $alerts->map(function ($alert) {
            $alert->priorite = $this->alerteService->calculatePriority($alert);
            $alert->prioriteColor = $this->alerteService->getPriorityColor($alert->priorite);
            $alert->daysUntilDue = Carbon::now()->diffInDays(Carbon::parse($alert->dateEchance), false);
            return $alert;
        });

        // Apply priority filter if requested
        if ($request->has('priorite')) {
            $alerts = $alerts->filter(function ($alert) use ($request) {
                return $alert->priorite === $request->priorite;
            })->values();
        }

        return response()->json($alerts);
    }

    /**
     * Get alerts statistics for dashboard
     */
    public function getStats()
    {
        $total = Alerte::where('statut', 'En attente')->count();
        $treated = Alerte::where('statut', 'Traité')->count();
        
        // Get all pending alerts with priority
        $pendingAlerts = Alerte::with('voiture')
            ->where('statut', 'En attente')
            ->orderBy('dateEchance', 'asc')
            ->get()
            ->map(function ($alerte) {
                $alerte->priorite = $this->alerteService->calculatePriority($alerte);
                return $alerte;
            });

        // Count by priority
        $critique = $pendingAlerts->where('priorite', 'critique')->count();
        $haute = $pendingAlerts->where('priorite', 'haute')->count();
        $moyenne = $pendingAlerts->where('priorite', 'moyenne')->count();
        $faible = $pendingAlerts->where('priorite', 'faible')->count();

        // Get top 5 urgent alerts
        $urgentes = $pendingAlerts->take(5)->map(function ($alerte) {
            return [
                'idAlerte' => $alerte->idAlerte,
                'type' => $alerte->type,
                'date' => $alerte->dateEchance,
                'priorite' => $alerte->priorite,
                'prioriteColor' => $this->alerteService->getPriorityColor($alerte->priorite),
                'vehicule' => $alerte->voiture 
                    ? "{$alerte->voiture->marque} {$alerte->voiture->modele}"
                    : 'N/A'
            ];
        });

        return response()->json([
            'total' => $total,
            'treated' => $treated,
            'byPriority' => [
                'critique' => $critique,
                'haute' => $haute,
                'moyenne' => $moyenne,
                'faible' => $faible,
            ],
            'urgentes' => $urgentes
        ]);
    }

    /**
     * Create a new alert
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'dateEchance' => 'required|date',
            'kilometrageEchance' => 'nullable|numeric',
            'statut' => 'sometimes|string|in:En attente,Traité',
            'voiture_id' => 'required|exists:voitures,idVoiture',
        ]);

        // Set default status if not provided
        $validated['statut'] = $validated['statut'] ?? 'En attente';

        $alerte = Alerte::create($validated);
        $alerte->load('voiture');

        // Add priority
        $alerte->priorite = $this->alerteService->calculatePriority($alerte);
        $alerte->prioriteColor = $this->alerteService->getPriorityColor($alerte->priorite);

        return response()->json([
            'message' => 'Alerte créée avec succès',
            'alerte' => $alerte
        ], 201);
    }

    /**
     * Get a specific alert
     */
    public function show(string $id)
    {
        $alerte = Alerte::with(['voiture', 'voiture.interventions'])->findOrFail($id);
        
        // Add priority
        $alerte->priorite = $this->alerteService->calculatePriority($alerte);
        $alerte->prioriteColor = $this->alerteService->getPriorityColor($alerte->priorite);
        $alerte->daysUntilDue = Carbon::now()->diffInDays(Carbon::parse($alerte->dateEchance), false);

        return response()->json($alerte);
    }

    /**
     * Update an alert
     */
    public function update(Request $request, string $id)
    {
        $alerte = Alerte::findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|string|max:255',
            'dateEchance' => 'sometimes|date',
            'kilometrageEchance' => 'sometimes|numeric',
            'statut' => 'sometimes|string|in:En attente,Traité',
            'voiture_id' => 'sometimes|exists:voitures,idVoiture',
        ]);

        $alerte->update($validated);
        $alerte->load('voiture');

        // Add priority
        $alerte->priorite = $this->alerteService->calculatePriority($alerte);
        $alerte->prioriteColor = $this->alerteService->getPriorityColor($alerte->priorite);

        return response()->json([
            'message' => 'Alerte mise à jour avec succès',
            'alerte' => $alerte
        ]);
    }

    /**
     * Delete an alert
     */
    public function destroy(string $id)
    {
        $alerte = Alerte::findOrFail($id);
        $alerte->delete();

        return response()->json([
            'message' => 'Alerte supprimée avec succès'
        ], 200);
    }

    /**
     * Mark an alert as resolved
     */
    public function resolve(string $id)
    {
        $alerte = Alerte::findOrFail($id);
        $this->alerteService->resolveAlert($alerte);

        $alerte->load('voiture');

        return response()->json([
            'message' => 'Alerte marquée comme traitée',
            'alerte' => $alerte
        ]);
    }

    /**
     * Generate alerts for all vehicles
     */
    public function generateAlerts()
    {
        try {
            $count = $this->alerteService->generateAllAlerts();
            
            // Notify critical alerts (wrapped in try-catch to prevent blocking)
            try {
                $criticalAlerts = Alerte::with('voiture')->get()->filter(function ($alerte) {
                    return $this->alerteService->calculatePriority($alerte) === 'critique';
                });
                
                foreach ($criticalAlerts as $alerte) {
                    $this->notificationService->notifyAlertGenerated($alerte);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to send some notifications: ' . $e->getMessage());
            }

            return response()->json([
                'message' => "Génération des alertes terminée",
                'alertsGenerated' => $count
            ]);
        } catch (\Exception $e) {
            \Log::error('Alert generation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la génération des alertes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate alerts for a specific vehicle
     */
    public function generateForVehicle(string $voitureId)
    {
        $voiture = Voiture::findOrFail($voitureId);
        $count = $this->alerteService->generateAlertsForVehicle($voiture);

        return response()->json([
            'message' => "Alertes générées pour le véhicule {$voiture->marque} {$voiture->modele}",
            'alertsGenerated' => $count
        ]);
    }

    /**
     * Clean up old resolved alerts
     */
    public function cleanup()
    {
        $count = $this->alerteService->cleanupOldAlerts();

        return response()->json([
            'message' => "Nettoyage terminé",
            'alertsDeleted' => $count
        ]);
    }
}
