<?php


namespace App\Actions;


use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\User;
use App\Services\mail\send_email;

class SendOTP
{
    public static function send($user){
        if($user == null){
            return messages::error_output(trans('errors.not_found_user'));
        }

        //$user->activation_code = rand(1000,9999);
        $user->activation_code = 1234;
        $user->save();
        if($user->email != ''){
            $msg = 'رقم التحقق (otp) الخاص بك هو '.$user->activation_code;
            send_email::send(
                trans('keywords.activation_email_code'),
                trans('keywords.activation_email_code_body_message'),
                '', $msg, $user['email']
            );
        }else{
            // send phone otp
        }
        return messages::success_output(trans('messages.sent_otp_successfully'),UserResource::make($user));
    }
}
