<?php


namespace App\Actions;


use App\Models\payments;
use App\Models\taxes;

class PaymentModalSave
{
    public static function make($id,$model_name,$visa,$money){
        $tax = taxes::query()->first()->percentage;
        echo "money = ".$money."<br>";
        $total = $money +  ($money * $tax / 100 );
        echo "total = ".$total."<br>";
        payments::query()->create([
            'paymentable_id'=>$id,
            'paymentable_type'=>'App\Models\\'.$model_name,
            'visa_id'=>$visa,
            'money'=>$total,
            'tax'=>$tax
        ]);
        return true;
    }
}
