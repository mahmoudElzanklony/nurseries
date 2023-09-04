<?php


namespace App\Actions;


use App\Models\bank_model_payment;

class BankModelPayment
{
    public static function make($id,$model_name,$type_of_transfer,$bank_model,$image_file){
        bank_model_payment::query()->create([
            'bank_model_payment_id'=>$id,
            'bank_model_payment_type'=>'App\Models\\'.$model_name,
            'sender_id'=>auth()->id(),
            'receiver_id'=>$bank_model['receiver_id'],
            'type_of_transfer'=>$type_of_transfer,
            'name'=>$bank_model['name'],
            'date_of_transfer'=>$bank_model['date_of_transfer'],
            'number_sent_from'=>$bank_model['number_sent_from'],
            'number_sent_to'=>$bank_model['number_sent_to'],
            'image'=>$image_file,
        ]);
        return true;
    }
}
