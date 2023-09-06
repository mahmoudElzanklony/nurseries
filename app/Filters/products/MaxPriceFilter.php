<?php


namespace App\Filters\products;
use Closure;

class MaxPriceFilter
{
    public function handle($request, Closure $next){
        if(request()->has('max_price')){
            return $next($request)->where('main_price','<=',request('max_price'));
        }
        return $next($request);
    }
}
