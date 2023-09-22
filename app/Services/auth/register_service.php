<?php


namespace App\Services\auth;


use App\Actions\SendOTP;
use App\Models\countries;
use App\Models\marketer_clients;
use App\Models\roles;
use App\Models\User;
use App\Http\traits\messages;
use App\Models\user_company_info;
use App\Models\user_info;
use App\Services\DB_connections;
use App\Services\mail\send_email;

class register_service
{
    use messages;
    public static function register_process($req,$validated){
        $user_info = $validated;
        // check if role exist in roles or not
        if($req['type'] == 'client'|| $req['type'] == 'company' || $req['type'] == 'seller') {
            if($req['register_by'] == 'app') {
                $check_user = User::query()->where('phone', '=', $req['phone'])->first();
            }else{
                $check_user = User::query()->where('email', '=', $req['email'])->first();
            }
            if($check_user != null){
                // already exists
                return SendOTP::send($check_user);
            }else{
                $role = roles::query()->where('name', $req['type'])->first();
                // role is correct
                if ($role != null) {
                    if($req['register_by'] == 'app') {
                        $country = countries::query()->where('code','=',$req['country_code'])->first();
                        $user_info['phone'] = $req['phone'];
                    }else{
                        $country = countries::query()->first();
                    }

                    $user_info['activation_code'] = rand(1000,9999);
                    $user_info['role_id'] = $role->id;
                    $user_info['country_id'] = $country->id ?? 1;
                    $user_info['new_user'] = true;

                    $user_info['register_by'] = $req['register_by'];
                    // create new user
                    $user = User::query()->create($user_info);
                    SendOTP::send($user);



                    return self::success_output(trans('messages.registered_user'),$user);
                } else {
                    // role isn't correct
                    return self::error_output(self::errors(['type' => trans('messages.err_invalid_type')]));
                }
            }

        }else{
            return self::error_output(self::errors(['type' => trans('messages.err_invalid_type')]));
        }
    }
}
