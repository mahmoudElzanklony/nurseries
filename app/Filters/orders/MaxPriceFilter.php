<?php


namespace App\Filters\orders;
use Closure;

class MaxPriceFilter
{
    public function handle($request, Closure $next){
        if(request()->has('max_price')){
            return $next($request)->whereHas('payment',function($e){
               $e->where('money','<=',request('max_price'));
            });
        }
        return $next($request);
    }
}
