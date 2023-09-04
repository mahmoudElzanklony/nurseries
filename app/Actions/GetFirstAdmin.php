<?php


namespace App\Actions;


use App\Models\User;

class GetFirstAdmin
{
    public static function admin_info(){
        return User::query()->whereHas('role',function($e){
            $e->where('name','=','admin');
        })->first();
    }
}
