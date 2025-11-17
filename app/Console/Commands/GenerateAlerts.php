<?php

namespace App\Console\Commands;

use App\Services\AlerteService;
use Illuminate\Console\Command;

class GenerateAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:generate {--vehicle= : Generate alerts for specific vehicle ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate automatic alerts for all vehicles based on maintenance schedules and conditions';

    protected $alerteService;

    /**
     * Create a new command instance.
     */
    public function __construct(AlerteService $alerteService)
    {
        parent::__construct();
        $this->alerteService = $alerteService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting alert generation...');

        $vehicleId = $this->option('vehicle');

        if ($vehicleId) {
            // Generate for specific vehicle
            $voiture = \App\Models\Voiture::find($vehicleId);
            
            if (!$voiture) {
                $this->error("Vehicle with ID {$vehicleId} not found.");
                return 1;
            }

            $count = $this->alerteService->generateAlertsForVehicle($voiture);
            $this->info("Generated {$count} alerts for vehicle {$voiture->marque} {$voiture->modele}");
        } else {
            // Generate for all vehicles
            $count = $this->alerteService->generateAllAlerts();
            $this->info("Generated {$count} alerts for all vehicles");
        }

        $this->info('Alert generation completed successfully!');
        return 0;
    }
}
