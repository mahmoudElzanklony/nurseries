<?php


namespace App\Services\users;


use App\Models\countries;

class get_country_of_user
{
    public static function get_country_obj(){
        if(auth()->check()){
            return countries::selection()->where('id','=',auth()->user()->country_id)->first();
        }else{
            return '';
        }
    }
}
