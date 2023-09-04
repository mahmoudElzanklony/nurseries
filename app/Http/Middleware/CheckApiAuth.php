<?php

namespace App\Http\Middleware;

use App\Http\traits\messages;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
class CheckApiAuth extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('auth-token');
        $request->headers->set('auth-token',(string)$token,true);
        $request->headers->set('Authorization','Bearer '.$token,true);
        try {
            $user = $this->auth->parseToken()->authenticate($request);
            if($user == false){
                return messages::error_output(['invalid credential']);
            }
        }catch (\Exception $e){
            return messages::error_output([$e->getMessage()]);
        }
         $user['token'] = $token;
        // return  response(['data'=>$user]);

        return $next($request);
    }
}
