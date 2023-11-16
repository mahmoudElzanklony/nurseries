<?php


namespace App\Actions;


use App\Models\User;

class SellerInfoWithAllData
{
    public static function get(){
        return User::query()->with(['bank_info','store_info','commercial_info'=>function($e){
            $e->with('images');
        }]);
    }
}
