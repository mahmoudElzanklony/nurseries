<?php


namespace App\Actions;


use App\Models\payments;

class PaymentModalSave
{
    public static function make($id,$model_name,$visa,$money){
        payments::query()->create([
            'paymentable_id'=>$id,
            'paymentable_type'=>'App\Models\\'.$model_name,
            'visa_id'=>$visa,
            'money'=>$money,
        ]);
        return true;
    }
}
