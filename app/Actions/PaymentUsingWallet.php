<?php


namespace App\Actions;


use App\Http\traits\messages;
use App\Models\reports;
use App\Models\User;

class PaymentUsingWallet
{
    public static function wallet_payment($price_will_be_taken,$report_info = '',$report_type = ''){
        $user = auth()->user();
        if($user->wallet >= $price_will_be_taken){
            // i have enough money in my wallet ==> withdraw from it
            $user = User::query()->find(auth()->id());
            $user->wallet = $user->wallet - $price_will_be_taken;
            $user->save();
            // insert now in report that i withdraw money from my wallet
            if($report_info != '') {
                reports::query()->create([
                    'user_id' => auth()->id(),
                    'info' => $report_info,
                    'type' => $report_type,
                ]);
            }
            return true;
        }else{
            return false;
        }
    }
}
