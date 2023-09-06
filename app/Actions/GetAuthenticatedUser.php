<?php


namespace App\Actions;


use App\Http\traits\messages;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetAuthenticatedUser
{
    public static function get_info(){
        if(request()->hasHeader('token')) {
            $token = request()->header('token');
            request()->headers->set('token', (string)$token, true);
            request()->headers->set('Authorization', 'Bearer ' . $token, true);
            try {
                $token = JWTAuth::parseToken();
                $user = $token->authenticate();
                if ($user == false) {
                    return messages::error_output(['invalid credential']);
                }
            } catch (\Exception $e) {
                return messages::error_output([$e->getMessage()]);
            }
        }
        return $user ?? null;
    }
}
