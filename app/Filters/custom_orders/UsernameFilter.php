<?php


namespace App\Filters\custom_orders;
use Closure;

class UsernameFilter
{
    public function handle($request, Closure $next){
        if(request()->has('seller_name')){
            return $next($request)->whereHas('seller',function($e){
                $e->where('username','LIKE','%'.request('seller_name').'%');
            });
        }
        return $next($request);
    }
}
