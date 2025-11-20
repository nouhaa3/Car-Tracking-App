<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Generate alerts daily at 6:00 AM
        $schedule->command('alerts:generate')->dailyAt('06:00');
        
        // Clean up old resolved alerts weekly on Sunday at 2:00 AM
        $schedule->command('alerts:generate --cleanup')->weeklyOn(0, '02:00');
        
        // Check notifications (document expiration, upcoming interventions) daily at 7:00 AM
        $schedule->command('notifications:check')->dailyAt('07:00');
        
        // Clean up old read notifications weekly on Monday at 3:00 AM
        $schedule->command('notifications:check --cleanup')->weeklyOn(1, '03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
