<?php

namespace App\Console;

use App\Console\Commands\UpdateExpiredOTPs;
use App\Console\Commands\MoveOTPToHistory;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // Schedule to run the UpdateExpiredOTPs command every minute
        // $schedule->command('otp:update-expired')->everyMinute();

        // Schedule to run the MoveOTPToHistory command daily at midnight
        $schedule->command('otp:move-to-history')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
