<?php

namespace App\Console;

use App\Actions\ManageTimeAlert;
use App\Actions\SendNotification;
use App\Console\Commands\MigrateClientDB;
use App\Jobs\ManageCreateTransactionsJob;
use App\Models\operations;
use App\Models\tenants;
use App\Models\users_products_care_alerts;
use App\Services\DB_connections;
use Carbon\Carbon;
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
        //
        MigrateClientDB::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('alerts:manage')
               ->everyMinute()
               ->withoutOverlapping()
               ->runInBackground();

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
