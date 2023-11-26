<?php

namespace App\Console\Commands;

use App\Models\notifications;
use App\Models\notifications_jobs;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotificationsManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:manage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $data = notifications_jobs::query()->where('status','=','pending')->get();
        $admin = User::query()->whereHas('role',function($e){
            $e->where('name','=','admin');
        })->first();
        foreach($data as $datum){
            if(date('Y-m-d') == $datum->send_at){
                $datum->status = 'completed';
                // get type
                $types = explode(',',$datum->user_type);
                foreach($types as $type){
                    $users = User::query()->whereHas('role',function($e) use ($type){
                        $e->where('name','=',$type);
                    })->selectRaw('id')->get();
                    $output = [];
                    foreach ($users as $user){
                        $values = [
                            'sender_id'=>$admin->id,
                            'receiver_id'=>$user->id,
                            'ar_content'=>$datum->content,
                            'created_at'=>now(),
                            'updated_at'=>now(),
                        ];array_push($output,$values);
                    }
                    if(sizeof($output) > 0) {
                        DB::table('notifications')->insert($output);
                    }
                }
                $datum->save();
            }
        }
    }
}
