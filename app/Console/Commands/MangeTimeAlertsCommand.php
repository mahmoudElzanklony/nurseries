<?php

namespace App\Console\Commands;

use App\Actions\ManageTimeAlert;
use App\Actions\SendNotification;
use App\Models\users_products_care_alerts;
use Illuminate\Console\Command;

class MangeTimeAlertsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:manage';

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
        $alerts = users_products_care_alerts::query()
            ->with('product_care',function ($e){
                $e->with('care');
            })
            ->with('product_care.product')
            ->get();
        foreach($alerts as $alert){

            $check = ManageTimeAlert::check_send_alert($alert->next_alert);
            if($check){
                // send Notification
                $name = $alert->product_care->product;
                $info = [
                    'ar'=> 'تنبيه !! موعد '.$alert->product_care->care->ar_name ?? ''.' الخاصه ب' .$name->ar_name,
                    'en'=> 'Alert !! '.$alert->product_care->care->{'en_name'} ?? ''.'Related to ' .$name->en_name
                ];
                SendNotification::to_any_one_else_admin($alert->user_id,$info,'/profile/alerts');
                $alert->update([
                    'next_alert'=>ManageTimeAlert::manage($alert->product_care->time_number,$alert->product_care->time_type,$alert->next_alert)
                ]);
            }

        }

    }
}
