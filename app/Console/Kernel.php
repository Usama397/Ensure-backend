<?php

namespace App\Console;

use App\Console\Commands\FacebookCampaigns;
use App\Console\Commands\FacebookOld;
use App\Console\Commands\GenerateMonthly;
use App\Console\Commands\GoogleCampaigns;
use App\Console\Commands\GoogleOld;
use App\Console\Commands\FacebookDaily;
use App\Console\Commands\GoogleDaily;
use App\Console\Commands\GoogleToken;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\MatchMakingCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('facebook:campaigns')->hourly();
        // $schedule->command('google:campaigns')->hourly();
        // $schedule->command('facebook:daily')->everyThirtyMinutes();
        // $schedule->command('google:daily')->everyThirtyMinutes();
        // $schedule->command('generate:monthly')->everyThirtyMinutes();
        
        $schedule->command('matchmaking:cron')->everyMinute();
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
