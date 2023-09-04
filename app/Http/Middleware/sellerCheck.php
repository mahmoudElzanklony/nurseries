<?php

namespace App\Http\Middleware;

use App\Http\traits\messages;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class sellerCheck
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
        if(auth()->check()){
            $user = User::query()->with('role')->find(auth()->id());
            if($user->role->name == 'seller'){
                return $next($request);
            }else{
                return messages::error_output('you dont have permission to access this !!');
            }

        }
    }
}
