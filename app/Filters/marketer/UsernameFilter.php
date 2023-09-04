<?php


namespace App\Filters\marketer;

use Closure;
use Illuminate\Support\Str;

class UsernameFilter
{
    public function handle($request, Closure $next){
        if (! request()->has('username')) {
            return $next($request);
        }

        return $next($request)->whereHas('package_order',function($u){
            $u->whereHas('user',function($c){
                $c->where('username','LIKE','%'.request()->input('username').'%');
            });
        });

    }
}
