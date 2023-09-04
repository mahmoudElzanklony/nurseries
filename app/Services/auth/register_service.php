<?php


namespace App\Services\auth;


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
        if($req['type'] == 'client' || $req['type'] == 'marketer') {
            $role = roles::query()->where('name', $req['type'])->first();
            // role is correct
            if ($role != null) {
                $user_info['address'] = '';
                $user_info['activation_code'] = time();
                $user_info['wallet'] = 0;
                $user_info['role_id'] = $role->id;
                $user_info['password'] = bcrypt($user_info['password']);
                // create new user
                $user = User::query()->create($user_info);

                // send email to user
                send_email::send(
                    trans('keywords.activation_email_code'),
                    trans('keywords.activation_email_code_body_message'),
                    request()->root().'/?activation='.$user_info['activation_code'],
                    'press here',$user_info['email']
                );

                // check if role name is client
                // mohamed_1234
                if($role->name == 'client'){
                    // create DB if type is client
                    $db_name = str_replace(
                        substr($user_info['email'],
                            strpos($user_info['email'], '@')), '_'.$user_info['activation_code'], $user_info['email']);
                    // create database for client
                    $user_info['user_id'] = $user->id;

                    // check if client register from marketer
                    if(isset($req['marketer_id'])){
                        // check marketer id is true
                        $marketer = User::query()->find($req['marketer_id']);
                        if($marketer != null) {
                            marketer_clients::query()->create([
                                'marketer_id' => $req['marketer_id'],
                                'client_id' => $user->id,
                            ]);
                        }
                    }

                    DB_connections::create_db($db_name,$user_info);
                }

                return self::success_output(trans('messages.registered_user'));
            } else {
                // role isn't correct
                return self::error_output(self::errors(['type' => trans('messages.err_invalid_type')]));
            }
        }else{
            return self::error_output(self::errors(['type' => trans('messages.err_invalid_type')]));
        }
    }
}
