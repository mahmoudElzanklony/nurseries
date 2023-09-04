<?php


namespace App\Actions;


use App\Http\Requests\bankModelFormRequest;
use App\Http\traits\messages;
use App\Models\bank_model_payment;
use App\Models\notifications;
use App\Models\packages;
use App\Models\packages_orders;
use App\Models\reports;
use App\Models\User;
use App\Services\RegisteredFromMarketer;

class MakePackageOrder
{
    public static function make_order($data,$bank_model,$image_file){
        $package = packages::query()->find($data['package_id']);
        if($data['type_of_transfer'] == 'wallet'){
            // payment using wallet
            $report_info = trans('messages.withdraw_wallet_to_buy_package').' '.$package->price;
            $return_payment_wallet = PaymentUsingWallet::wallet_payment($package->price,$report_info,'wallet(withdraw)');
            if($return_payment_wallet == false){
                return messages::error_output(trans('errors.you_dont_have_enough_wallet_money'));
            }
        }
        $order_info = [
            'package_id'=>$package->id,
            'user_id'=>auth()->id(),
            'price'=>$package->price,
            'package_points'=>$package->no_points,
            'current_points'=>$package->no_points,
            'payment_type'=>$data['type_of_transfer'],
            'status'=>($data['type_of_transfer'] == 'visa' || $data['type_of_transfer'] == 'wallet') ? 1:0, // 1 => accepted , 0 pending
        ];
        // make new package order
        $order = packages_orders::query()->create($order_info);
        // report about payment
        $payment_success_msg = trans('messages.package_payment').' '.$package->name.' '.trans('messages.successfully');
        reports::query()->create([
            'user_id'=>auth()->id(),
            'info'=>$payment_success_msg,
            'type'=>'package(buy)',
        ]);

        // check if this user register from any marketer
        $marketer = RegisteredFromMarketer::get_marketer_if_registered_by_him(auth()->id());
        if($marketer != null){
            RegisteredFromMarketer::give_profit_fo_marketer($marketer->marketer_id,
                $order->id,
                ($data['type_of_transfer'] == 'visa' || $data['type_of_transfer'] == 'wallet')?'ready_to_take':'pending');
            // assign profit of marker into report
            $profit_marketer = trans('messages.profit_marketer').' '.$marketer->marketer->username.' '.trans('messages.result_of_payment_package').$package->name.' '.trans('messages.successfully');
            reports::query()->create([
                'user_id'=>$marketer->marketer_id,
                'info'=>$profit_marketer,
                'type'=>'package(marketer_withdraw_pending)',
            ]);
        }

        // send notification to admin
        SendNotification::to_admin(auth()->id(),$payment_success_msg);
        // send notification to client
        SendNotification::to_any_one_else_admin(auth()->id(),$payment_success_msg);

        if($data['type_of_transfer'] == 'mobile' || $data['type_of_transfer'] == 'bank'){
            // insert new modal bank info
            $bank_model['receiver_id'] = GetFirstAdmin::admin_info()->id;
            BankModelPayment::make($order->id,
                'packages_orders',
                'client_package'.$data['type_of_transfer']
                ,$bank_model,$image_file);


        }
        return messages::success_output($payment_success_msg);
    }
}
