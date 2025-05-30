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
        // $schedule->command('inspire')->hourly();

        \log::info('Scheduling tasks...');

        $schedule->command('send:task-follow-up-links')
        ->dailyAt('09:10')   // Tous les jours à 08h UTC+2
        ->when(function () {
            return !now()->isSunday(); // Sauf le dimanche
        });

        $schedule->command('tasks:reassign-unfinished')
        ->mondays()
        ->at('07:45');
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
