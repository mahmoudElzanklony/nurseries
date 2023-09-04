<?php


namespace App\Actions;


use App\Http\traits\messages;
use App\Models\packages;
use App\Models\payment_actions;
use App\Models\reports;

class DoPaymentProcess
{
    public static function order_info($data){
        return  [
            'payment_id'=>auth()->id(),
            'payment_type'=>'App\Models\\'.($data['model_name'] ?? 'users') ,
            'money'=>$data['money'],
            'type'=>$data['payment_type'], // wallet(client) mean client charge , wallet(admin) , service
            'status'=>($data['type_of_transfer'] == 'visa') ? 1:0, // 1 => accepted , 0 pending
        ];

    }

    public static function make($data,$bank_model,$image_file){
        $order_info = self::order_info($data);
        // make new charge wallet
        $order = payment_actions::query()->create($order_info);
        // report about payment
        $payment_success_msg = $data['info_report_about_success_payment'];
        reports::query()->create([
            'user_id'=>auth()->id(),
            'info'=>$payment_success_msg,
            'type'=>'wallet(add)',
        ]);
        // send notification to admin
        SendNotification::to_admin(auth()->id(),$payment_success_msg);
        // send notification to client
        SendNotification::to_any_one_else_admin(auth()->id(),$payment_success_msg);

        if($data['type_of_transfer'] == 'mobile' || $data['type_of_transfer'] == 'bank'){
            // insert new modal bank info
            $bank_model['receiver_id'] = GetFirstAdmin::admin_info()->id;
            BankModelPayment::make($order->id,
                'payment_actions',
                'client_package_'.$data['type_of_transfer']
                ,$bank_model,$image_file);


        }
        return messages::success_output($payment_success_msg);
    }
}
