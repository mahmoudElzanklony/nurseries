<?php


namespace App\Services;


use App\Models\marketer_clients;
use App\Models\marketer_profit;

class RegisteredFromMarketer
{
    public static function get_marketer_if_registered_by_him($id){
        return  marketer_clients::query()->with('marketer')->where('client_id','=',$id)->first();
    }

    public static function give_profit_fo_marketer($marketer_id,$package_order_id,$status){
        marketer_profit::query()->create([
           'marketer_id'=>$marketer_id,
           'package_order_id'=>$package_order_id,
           'status'=>$status
        ]);
    }
}
