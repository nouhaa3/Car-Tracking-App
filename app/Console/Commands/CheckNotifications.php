<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:check 
                          {--cleanup : Clean up old read notifications}';

    /**
     * The console command description.
     */
    protected $description = 'Check for expiring documents and upcoming interventions, send notifications';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Starting notification checks...');
        $this->newLine();

        // Check expiring documents
        $this->info('📄 Checking document expirations...');
        $this->notificationService->checkExpiringDocuments();
        $this->line('   ✓ Document expiration checks complete');
        
        // Check upcoming interventions
        $this->info('🔧 Checking upcoming interventions...');
        $this->notificationService->checkUpcomingInterventions();
        $this->line('   ✓ Intervention checks complete');

        // Cleanup if requested
        if ($this->option('cleanup')) {
            $this->info('🧹 Cleaning up old notifications...');
            $this->notificationService->cleanupOldNotifications(30);
            $this->line('   ✓ Old notifications removed');
        }

        $this->newLine();
        $this->info('✅ All notification checks complete!');
        
        return Command::SUCCESS;
    }
}
