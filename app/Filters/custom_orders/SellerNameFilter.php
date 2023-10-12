<?php


namespace App\Filters\custom_orders;
use Closure;

class SellerNameFilter
{
    public function handle($request, Closure $next){
        if(request()->has('seller_name')){
            return $next($request)->whereHas('sellers_alerts.seller',function($e){
               $e->where('username','LIKE','%'.request('seller_name').'%');
            });
        }
        return $next($request);
    }
}
