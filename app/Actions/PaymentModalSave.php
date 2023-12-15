<?php


namespace App\Actions;


use App\Models\payments;
use App\Models\taxes;

class PaymentModalSave
{
    public static function make($id,$model_name,$visa,$money){
        $tax = taxes::query()->first()->percentage;
        payments::query()->create([
            'paymentable_id'=>$id,
            'paymentable_type'=>'App\Models\\'.$model_name,
            'visa_id'=>$visa,
            'money'=>$money +  ($money * $tax / 100 ),
            'tax'=>$tax
        ]);
        return true;
    }
}
