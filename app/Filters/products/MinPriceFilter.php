<?php


namespace App\Filters\products;
use Closure;

class MinPriceFilter
{
    public function handle($request, Closure $next){
        if(request()->has('min_price')){
            return $next($request)->where('main_price','>=',request('min_price'));
        }
        return $next($request);
    }
}
