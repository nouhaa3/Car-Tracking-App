<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alerte;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;

class CheckAlertNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check-urgent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for urgent alerts (5 days or less) and notify admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for urgent alerts...');

        // Get pending alerts with due date within 5 days
        $urgentAlerts = Alerte::where('statut', 'En attente')
            ->whereDate('dateEchance', '<=', Carbon::now()->addDays(5))
            ->whereDate('dateEchance', '>=', Carbon::now())
            ->with('voiture')
            ->get();

        if ($urgentAlerts->isEmpty()) {
            $this->info('No urgent alerts found.');
            return 0;
        }

        // Get all admin users
        $admins = User::where('role_id', 1)->get();

        if ($admins->isEmpty()) {
            $this->warn('No admin users found.');
            return 0;
        }

        $notificationsCreated = 0;

        foreach ($urgentAlerts as $alert) {
            $daysRemaining = Carbon::now()->diffInDays(Carbon::parse($alert->dateEchance), false);
            
            foreach ($admins as $admin) {
                // Check if notification already exists for this alert and admin
                $existingNotification = Notification::where('user_id', $admin->id)
                    ->where('type', 'alert_urgent')
                    ->whereJsonContains('metadata->alert_id', (string)$alert->idAlerte)
                    ->where('created_at', '>=', Carbon::now()->subDays(1))
                    ->first();

                if (!$existingNotification) {
                    // Create notification
                    $vehicleInfo = $alert->voiture 
                        ? "{$alert->voiture->marque} {$alert->voiture->modele}" 
                        : "Véhicule inconnu";

                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'alert_urgent',
                        'title' => $daysRemaining === 0 
                            ? 'Alerte urgente - Échéance aujourd\'hui !' 
                            : "Alerte urgente - {$daysRemaining} jour(s) restant(s)",
                        'message' => "Alerte {$alert->type} pour {$vehicleInfo} - Échéance: " . Carbon::parse($alert->dateEchance)->format('d/m/Y'),
                        'metadata' => json_encode([
                            'alert_id' => $alert->idAlerte,
                            'alert_type' => $alert->type,
                            'due_date' => $alert->dateEchance,
                            'days_remaining' => $daysRemaining,
                            'vehicle_id' => $alert->voiture_id,
                            'vehicle_name' => $vehicleInfo,
                            'priority' => $alert->priorite,
                        ]),
                        'is_read' => false,
                    ]);

                    $notificationsCreated++;
                }
            }
        }

        $this->info("Found {$urgentAlerts->count()} urgent alert(s).");
        $this->info("Created {$notificationsCreated} notification(s) for admin users.");

        return 0;
    }
}
