<?php

namespace App\Console\Commands;

use App\Models\orders;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveDraftOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'draft:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete draft orders';

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
        $data = orders::query()
            ->where('is_draft','=',1)
            ->where('created_at', '<', Carbon::now()->subMinutes(15))->get();
        foreach($data as $datum){
            $datum->delete();
        }
    }
}
