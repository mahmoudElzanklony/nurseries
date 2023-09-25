<?php


namespace App\Actions;


use App\Models\user_addresses;

class DefaultAddress
{
    public static function get(){
        return user_addresses::query()
            ->where('user_id',auth()->id())->where('default_address','=',1)->first();
    }
}
