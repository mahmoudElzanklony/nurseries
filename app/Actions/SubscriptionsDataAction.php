<?php


namespace App\Actions;


use App\Models\orders;
use App\Models\users_packages;

class SubscriptionsDataAction
{
    public static function get($user_id = null, $time_type = null){
        $data = users_packages::query()
            ->join('payments','users_packages.id','=','payments.paymentable_id')
            ->where('payments.paymentable_type','=','App\Models\users_packages')
            ->selectRaw('users_packages.* , payments.money');
        return $data;
    }
}
