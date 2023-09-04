<?php

namespace App\Console\Commands;

use App\Jobs\ManageCreateTransactionsJob;
use App\Models\operations;
use App\Models\tenants;
use App\Services\DB_connections;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class processTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:process {operation} {db}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch transactions';

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
        DB_connections::connect_to_master();
        $database_ojb = tenants::query()->find($this->argument('db'));
        DB_connections::connect_to_tanent($database_ojb->database,[],true);
        $operation_obj = operations::query()
            ->with('period')
            ->with('query_cond')
            ->find($this->argument('operation'));
        foreach($operation_obj->period as $period){
            ManageCreateTransactionsJob::do_operation($operation_obj, $database_ojb , $period);
        }
    }
}
