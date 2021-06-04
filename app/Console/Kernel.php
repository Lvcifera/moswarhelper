<?php

namespace App\Console;

use App\Console\Commands\Kubovich;
use App\Http\Controllers\BotFunctionController;
use App\Models\Patrol;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Patrol::class,
        Commands\Shaurburgers::class,
        Commands\Kubovich::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('patrol:start')->everyFifteenMinutes();
        $schedule->command('shaurburgers:start')->everyFifteenMinutes();
        $schedule->command('patriot:start')->everyThirtyMinutes();
        $schedule->command('kubovich:start')->hourly();
        $schedule->command('resetKubovichCount:start')->dailyAt('21:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
