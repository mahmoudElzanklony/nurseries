<?php

namespace App\Console;

use App\Console\Commands\MigrateClientDB;
use App\Jobs\ManageCreateTransactionsJob;
use App\Models\operations;
use App\Models\tenants;
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
        DB_connections::connect_to_master();
        $allDB = tenants::query()->get();
        foreach($allDB as $db){
            // connect to client database
            DB_connections::connect_to_tanent($db->database,[],true);
            // get all operations for this client
            $operations = operations::query()->with('period',function($p){
                $p->where('save_type','=','create');
            })->orderBy('id','DESC')->get();

            // Run the tasks in parallel
            foreach($operations as $operation){
              //  echo 'transactions:process '.$operation->id.' '.$db->id.'\\n';
                echo "Start time for operation ==>".Carbon::now()."\n";

                $schedule->command('transactions:process '.$operation->id.' '.$db->id)
                    ->everyMinute()
                    ->withoutOverlapping()
                    ->runInBackground();
            }

        }



       // $schedule->command('transactions:process')->everyMinute();
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
