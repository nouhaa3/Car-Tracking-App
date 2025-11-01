<?php

namespace App\Console\Commands;

use App\Services\AlerteService;
use Illuminate\Console\Command;

class GenerateAlertsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:generate 
                            {--vehicle= : Generate alerts for a specific vehicle ID}
                            {--cleanup : Clean up old resolved alerts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate intelligent alerts for vehicle maintenance based on kilometrage, dates, and conditions';

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
        $this->info('🚀 Starting alert generation...');
        $this->newLine();

        // Handle cleanup option
        if ($this->option('cleanup')) {
            $this->info('🧹 Cleaning up old resolved alerts...');
            $deleted = $this->alerteService->cleanupOldAlerts();
            $this->info("✅ Cleaned up {$deleted} old alerts.");
            $this->newLine();
        }

        // Handle specific vehicle
        if ($vehicleId = $this->option('vehicle')) {
            $this->info("🔍 Generating alerts for vehicle ID: {$vehicleId}");
            
            $voiture = \App\Models\Voiture::find($vehicleId);
            
            if (!$voiture) {
                $this->error("❌ Vehicle with ID {$vehicleId} not found!");
                return 1;
            }

            $count = $this->alerteService->generateAlertsForVehicle($voiture);
            $this->info("✅ Generated {$count} alert(s) for {$voiture->marque} {$voiture->modele}");
            
        } else {
            // Generate for all vehicles
            $this->info('🔄 Generating alerts for all vehicles...');
            
            $voitures = \App\Models\Voiture::all();
            $totalCount = 0;
            
            $progressBar = $this->output->createProgressBar($voitures->count());
            $progressBar->start();

            foreach ($voitures as $voiture) {
                $count = $this->alerteService->generateAlertsForVehicle($voiture);
                $totalCount += $count;
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);
            
            $this->info("✅ Successfully generated {$totalCount} alert(s) for {$voitures->count()} vehicle(s)");
        }

        // Display summary statistics
        $this->newLine();
        $this->displayStatistics();

        return 0;
    }

    /**
     * Display alert statistics
     */
    protected function displayStatistics()
    {
        $this->info('📊 Alert Statistics:');
        $this->table(
            ['Status', 'Count'],
            [
                ['En attente', \App\Models\Alerte::where('statut', 'En attente')->count()],
                ['Traité', \App\Models\Alerte::where('statut', 'Traité')->count()],
                ['Total', \App\Models\Alerte::count()],
            ]
        );

        // Show top 5 urgent alerts
        $urgentAlerts = \App\Models\Alerte::with('voiture')
            ->where('statut', 'En attente')
            ->orderBy('dateEchance', 'asc')
            ->limit(5)
            ->get();

        if ($urgentAlerts->count() > 0) {
            $this->newLine();
            $this->warn('⚠️  Top 5 Urgent Alerts:');
            
            $tableData = $urgentAlerts->map(function ($alert) {
                return [
                    'ID' => $alert->idAlerte,
                    'Type' => $alert->type,
                    'Vehicle' => $alert->voiture ? "{$alert->voiture->marque} {$alert->voiture->modele}" : 'N/A',
                    'Due Date' => $alert->dateEchance,
                ];
            })->toArray();

            $this->table(
                ['ID', 'Type', 'Vehicle', 'Due Date'],
                $tableData
            );
        }
    }
}
