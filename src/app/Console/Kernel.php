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
        $schedule->command('import:new-cars')->hourly();
        $schedule->command('import:used-cars')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Load console commands from the Commands folder
        $this->load(__DIR__.'/Commands');

        // Include any routes/console.php commands
        require base_path('routes/console.php');
    }
}
