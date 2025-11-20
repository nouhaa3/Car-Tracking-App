<?php

namespace App\Services;

use App\Models\Alerte;
use App\Models\Voiture;
use App\Models\Intervention;
use App\Models\Notification;
use App\Mail\AlertGeneratedMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlerteService
{
    // Alert thresholds configuration
    const KILOMETRAGE_THRESHOLDS = [
        'Vidange' => 10000,
        'Révision' => 30000,
        'Changement pneus' => 50000,
        'Contrôle freins' => 20000,
        'Remplacement batterie' => 60000,
    ];

    const DATE_THRESHOLDS = [
        'Contrôle technique' => 365, // Days
        'Assurance' => 30,           // Days before expiration
        'Révision annuelle' => 365,
    ];

    /**
     * Generate all alerts for all vehicles
     */
    public function generateAllAlerts()
    {
        $alertsGenerated = 0;
        $voitures = Voiture::all();

        foreach ($voitures as $voiture) {
            $alertsGenerated += $this->generateAlertsForVehicle($voiture);
        }

        Log::info("Alert generation completed: {$alertsGenerated} alerts generated.");
        return $alertsGenerated;
    }

    /**
     * Generate alerts for a specific vehicle
     */
    public function generateAlertsForVehicle(Voiture $voiture)
    {
        $alertsGenerated = 0;

        // Generate kilometrage-based alerts
        $alertsGenerated += $this->checkKilometrageAlerts($voiture);

        // Generate date-based alerts
        $alertsGenerated += $this->checkDateAlerts($voiture);

        // Generate condition-based alerts
        $alertsGenerated += $this->checkConditionAlerts($voiture);

        // Generate intervention-based alerts
        $alertsGenerated += $this->checkInterventionAlerts($voiture);

        return $alertsGenerated;
    }

    /**
     * Check and generate kilometrage-based alerts
     */
    protected function checkKilometrageAlerts(Voiture $voiture)
    {
        $alertsGenerated = 0;

        foreach (self::KILOMETRAGE_THRESHOLDS as $type => $threshold) {
            // Get last intervention of this type
            $lastIntervention = Intervention::where('voiture_id', $voiture->idVoiture)
                ->where('type', $type)
                ->orderBy('date', 'desc')
                ->first();

            $kmSinceLastService = 0;
            $nextServiceKm = 0;

            if ($lastIntervention) {
                // Calculate km since last service
                $kmSinceLastService = $voiture->kilometrage - ($lastIntervention->kilometrage ?? $voiture->kilometrage);
                $nextServiceKm = ($lastIntervention->kilometrage ?? 0) + $threshold;
            } else {
                // No previous intervention, use vehicle's current km + threshold
                $nextServiceKm = $voiture->kilometrage + $threshold;
            }

            // Check if alert should be generated (within 1000km of threshold)
            if ($voiture->kilometrage >= ($nextServiceKm - 1000)) {
                // Check if alert already exists
                $existingAlert = Alerte::where('voiture_id', $voiture->idVoiture)
                    ->where('type', $type)
                    ->where('statut', 'En attente')
                    ->where('kilometrageEchance', $nextServiceKm)
                    ->first();

                if (!$existingAlert) {
                    // Calculate expected date (assume 1000km per month average)
                    $kmRemaining = $nextServiceKm - $voiture->kilometrage;
                    $monthsUntilService = max(1, ceil($kmRemaining / 1000));
                    $dateEcheance = Carbon::now()->addMonths($monthsUntilService);

                    $newAlert = Alerte::create([
                        'type' => $type,
                        'dateEchance' => $dateEcheance,
                        'kilometrageEchance' => $nextServiceKm,
                        'statut' => 'En attente',
                        'voiture_id' => $voiture->idVoiture,
                    ]);

                    // Send email notification (non-blocking)
                    try {
                        $this->sendAlertNotification($newAlert);
                    } catch (\Exception $e) {
                        Log::warning("Failed to send notification for alert {$newAlert->idAlerte}: " . $e->getMessage());
                    }

                    $alertsGenerated++;
                }
            }
        }

        return $alertsGenerated;
    }

    /**
     * Check and generate date-based alerts
     */
    protected function checkDateAlerts(Voiture $voiture)
    {
        $alertsGenerated = 0;

        // Contrôle technique (annual inspection)
        $lastCT = Intervention::where('voiture_id', $voiture->idVoiture)
            ->where('type', 'Contrôle technique')
            ->orderBy('date', 'desc')
            ->first();

        if ($lastCT) {
            $nextCTDate = Carbon::parse($lastCT->date)->addYear();
            $daysUntilCT = Carbon::now()->diffInDays($nextCTDate, false);

            // Alert if within 60 days
            if ($daysUntilCT <= 60 && $daysUntilCT > 0) {
                $existingAlert = Alerte::where('voiture_id', $voiture->idVoiture)
                    ->where('type', 'Contrôle technique')
                    ->where('statut', 'En attente')
                    ->whereDate('dateEchance', $nextCTDate)
                    ->first();

                if (!$existingAlert) {
                    $newAlert = Alerte::create([
                        'type' => 'Contrôle technique',
                        'dateEchance' => $nextCTDate,
                        'kilometrageEchance' => $voiture->kilometrage,
                        'statut' => 'En attente',
                        'voiture_id' => $voiture->idVoiture,
                    ]);
                    try {
                        $this->sendAlertNotification($newAlert);
                    } catch (\Exception $e) {
                        Log::warning("Failed to send notification for alert {$newAlert->idAlerte}: " . $e->getMessage());
                    }
                    $alertsGenerated++;
                }
            }
        } else {
            // No CT record, create alert for immediate inspection
            $existingAlert = Alerte::where('voiture_id', $voiture->idVoiture)
                ->where('type', 'Contrôle technique')
                ->where('statut', 'En attente')
                ->first();

            if (!$existingAlert) {
                $newAlert = Alerte::create([
                    'type' => 'Contrôle technique',
                    'dateEchance' => Carbon::now()->addDays(30),
                    'kilometrageEchance' => $voiture->kilometrage,
                    'statut' => 'En attente',
                    'voiture_id' => $voiture->idVoiture,
                ]);
                try {
                    $this->sendAlertNotification($newAlert);
                } catch (\Exception $e) {
                    Log::warning("Failed to send notification for alert {$newAlert->idAlerte}: " . $e->getMessage());
                }
                $alertsGenerated++;
            }
        }

        // Révision annuelle
        $lastRevision = Intervention::where('voiture_id', $voiture->idVoiture)
            ->where('type', 'Révision')
            ->orderBy('date', 'desc')
            ->first();

        if ($lastRevision) {
            $nextRevisionDate = Carbon::parse($lastRevision->date)->addYear();
            $daysUntilRevision = Carbon::now()->diffInDays($nextRevisionDate, false);

            if ($daysUntilRevision <= 30 && $daysUntilRevision > 0) {
                $existingAlert = Alerte::where('voiture_id', $voiture->idVoiture)
                    ->where('type', 'Révision annuelle')
                    ->where('statut', 'En attente')
                    ->whereDate('dateEchance', $nextRevisionDate)
                    ->first();

                if (!$existingAlert) {
                    $newAlert = Alerte::create([
                        'type' => 'Révision annuelle',
                        'dateEchance' => $nextRevisionDate,
                        'kilometrageEchance' => $voiture->kilometrage,
                        'statut' => 'En attente',
                        'voiture_id' => $voiture->idVoiture,
                    ]);
                    try {
                        $this->sendAlertNotification($newAlert);
                    } catch (\Exception $e) {
                        Log::warning("Failed to send notification for alert {$newAlert->idAlerte}: " . $e->getMessage());
                    }
                    $alertsGenerated++;
                }
            }
        }

        return $alertsGenerated;
    }

    /**
     * Check and generate condition-based alerts
     */
    protected function checkConditionAlerts(Voiture $voiture)
    {
        $alertsGenerated = 0;

        // Alert if vehicle is in bad condition
        if (strtolower($voiture->etat) === 'mauvais') {
            $existingAlert = Alerte::where('voiture_id', $voiture->idVoiture)
                ->where('type', 'État véhicule critique')
                ->where('statut', 'En attente')
                ->first();

            if (!$existingAlert) {
                $newAlert = Alerte::create([
                    'type' => 'État véhicule critique',
                    'dateEchance' => Carbon::now()->addDays(7),
                    'kilometrageEchance' => $voiture->kilometrage,
                    'statut' => 'En attente',
                    'voiture_id' => $voiture->idVoiture,
                ]);
                try {
                    $this->sendAlertNotification($newAlert);
                } catch (\Exception $e) {
                    Log::warning("Failed to send notification for alert {$newAlert->idAlerte}: " . $e->getMessage());
                }
                $alertsGenerated++;
            }
        }

        // Alert if vehicle has been in maintenance for too long
        if (strtolower($voiture->statut) === 'maintenance') {
            $oldestIntervention = Intervention::where('voiture_id', $voiture->idVoiture)
                ->where('statut', 'En cours')
                ->orderBy('date', 'asc')
                ->first();

            if ($oldestIntervention) {
                $daysInMaintenance = Carbon::parse($oldestIntervention->date)->diffInDays(Carbon::now());

                if ($daysInMaintenance > 14) {
                    $existingAlert = Alerte::where('voiture_id', $voiture->idVoiture)
                        ->where('type', 'Maintenance prolongée')
                        ->where('statut', 'En attente')
                        ->first();

                    if (!$existingAlert) {
                        $newAlert = Alerte::create([
                            'type' => 'Maintenance prolongée',
                            'dateEchance' => Carbon::now()->addDays(3),
                            'kilometrageEchance' => $voiture->kilometrage,
                            'statut' => 'En attente',
                            'voiture_id' => $voiture->idVoiture,
                        ]);
                        try {
                            $this->sendAlertNotification($newAlert);
                        } catch (\Exception $e) {
                            Log::warning("Failed to send notification for alert {$newAlert->idAlerte}: " . $e->getMessage());
                        }
                        $alertsGenerated++;
                    }
                }
            }
        }

        return $alertsGenerated;
    }

    /**
     * Check and generate intervention-based alerts
     */
    protected function checkInterventionAlerts(Voiture $voiture)
    {
        $alertsGenerated = 0;

        // Check for high total maintenance costs
        $totalCosts = Intervention::where('voiture_id', $voiture->idVoiture)
            ->where('date', '>=', Carbon::now()->subMonths(6))
            ->sum('cout');

        if ($totalCosts > 10000) { // More than 10,000 DT in 6 months
            $existingAlert = Alerte::where('voiture_id', $voiture->idVoiture)
                ->where('type', 'Coûts élevés')
                ->where('statut', 'En attente')
                ->first();

            if (!$existingAlert) {
                $newAlert = Alerte::create([
                    'type' => 'Coûts élevés',
                    'dateEchance' => Carbon::now()->addDays(7),
                    'kilometrageEchance' => $voiture->kilometrage,
                    'statut' => 'En attente',
                    'voiture_id' => $voiture->idVoiture,
                ]);
                try {
                    $this->sendAlertNotification($newAlert);
                } catch (\Exception $e) {
                    Log::warning("Failed to send notification for alert {$newAlert->idAlerte}: " . $e->getMessage());
                }
                $alertsGenerated++;
            }
        }

        // Check for recurring issues (same type of intervention multiple times)
        $recentInterventions = Intervention::where('voiture_id', $voiture->idVoiture)
            ->where('date', '>=', Carbon::now()->subMonths(3))
            ->get()
            ->groupBy('type');

        foreach ($recentInterventions as $type => $interventions) {
            if ($interventions->count() >= 3) {
                $existingAlert = Alerte::where('voiture_id', $voiture->idVoiture)
                    ->where('type', 'Problème récurrent: ' . $type)
                    ->where('statut', 'En attente')
                    ->first();

                if (!$existingAlert) {
                    $newAlert = Alerte::create([
                        'type' => 'Problème récurrent: ' . $type,
                        'dateEchance' => Carbon::now()->addDays(7),
                        'kilometrageEchance' => $voiture->kilometrage,
                        'statut' => 'En attente',
                        'voiture_id' => $voiture->idVoiture,
                    ]);
                    try {
                        $this->sendAlertNotification($newAlert);
                    } catch (\Exception $e) {
                        Log::warning("Failed to send notification for alert {$newAlert->idAlerte}: " . $e->getMessage());
                    }
                    $alertsGenerated++;
                }
            }
        }

        return $alertsGenerated;
    }

    /**
     * Calculate alert priority
     */
    public function calculatePriority(Alerte $alerte): string
    {
        $daysUntilDue = Carbon::now()->diffInDays(Carbon::parse($alerte->dateEchance), false);

        // Critical types
        $criticalTypes = ['État véhicule critique', 'Contrôle technique', 'Problème récurrent'];
        if (in_array($alerte->type, $criticalTypes)) {
            return 'critique';
        }

        // Based on days until due
        if ($daysUntilDue < 0) {
            return 'critique'; // Overdue
        } elseif ($daysUntilDue <= 7) {
            return 'haute';
        } elseif ($daysUntilDue <= 30) {
            return 'moyenne';
        } else {
            return 'faible';
        }
    }

    /**
     * Get priority color
     */
    public function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            'critique' => '#E74C3C',
            'haute' => '#E67E22',
            'moyenne' => '#F39C12',
            'faible' => '#3498DB',
            default => '#95A5A6',
        };
    }

    /**
     * Mark alert as resolved
     */
    public function resolveAlert(Alerte $alerte)
    {
        $alerte->update(['statut' => 'Traité']);
        Log::info("Alert {$alerte->idAlerte} resolved for vehicle {$alerte->voiture_id}");
    }

    /**
     * Clean up old resolved alerts
     */
    public function cleanupOldAlerts(int $daysOld = 90)
    {
        $deleted = Alerte::where('statut', 'Traité')
            ->where('updated_at', '<', Carbon::now()->subDays($daysOld))
            ->delete();

        Log::info("Cleaned up {$deleted} old alerts.");
        return $deleted;
    }

    /**
     * Send email notification for a new alert
     */
    protected function sendAlertNotification(Alerte $alerte)
    {
        try {
            // Get the vehicle owner's email
            if ($alerte->voiture && $alerte->voiture->user && $alerte->voiture->user->email) {
                $priority = $this->calculatePriority($alerte);
                $priorityColor = $this->getPriorityColor($priority);

                // Send email
                Mail::to($alerte->voiture->user->email)
                    ->send(new AlertGeneratedMail($alerte, $priority, $priorityColor));

                Log::info("Email notification sent for alert {$alerte->idAlerte} to {$alerte->voiture->user->email}");
                
                // Create in-app notification
                Notification::create([
                    'user_id' => $alerte->voiture->user_id,
                    'type' => 'alert',
                    'title' => 'Nouvelle alerte: ' . $alerte->type,
                    'message' => "Votre véhicule {$alerte->voiture->marque} {$alerte->voiture->modele} nécessite une attention. Échéance: " . Carbon::parse($alerte->dateEchance)->format('d/m/Y'),
                    'link' => '/alertes/' . $alerte->idAlerte,
                    'read' => false,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification for alert {$alerte->idAlerte}: " . $e->getMessage());
        }
    }
}
