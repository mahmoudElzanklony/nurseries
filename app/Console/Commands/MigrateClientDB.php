<?php

namespace App\Console\Commands;

use App\Services\DB_connections;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MigrateClientDB extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mig:tenant {database_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrate tables to tenant database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data_base_name = $this->argument('database_name');
        DB_connections::connect_to_tanent($data_base_name);
        Artisan::call('migrate --path=database/migrations/client/ --database=tenant');
        return 0;
    }
}
