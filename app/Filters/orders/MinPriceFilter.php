<?php


namespace App\Filters\orders;
use Closure;

class MinPriceFilter
{
    public function handle($request, Closure $next){
        if(request()->has('min_price')){
            return $next($request)->whereHas('payment',function($e){
                $e->where('money','>=',request('min_price'));
            });
        }
        return $next($request);
    }
}
