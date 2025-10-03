<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Generate attendance records every workday at 6 AM
        $schedule->command('attendance:generate')
            ->weekdays()
            ->at('06:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/attendance.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}