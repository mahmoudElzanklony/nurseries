<?php


namespace App\Actions;


use App\Models\user_addresses;

class DefaultAddress
{
    public static function get(){
        return user_addresses::query()->with('area',function($e){
          $e->with('city',function($c){
             $c->with('government');
          });
        })->where('user_id',auth()->id())->where('default_address','=',1)->first();
    }
}
