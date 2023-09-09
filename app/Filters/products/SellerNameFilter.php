<?php


namespace App\Filters\products;
use Closure;

class SellerNameFilter
{
    public function handle($request, Closure $next){
        if(request()->has('seller_name')){
            return $next($request)
                ->whereHas('user',function($e){
                    $e->where('username','LIKE','%'.request('seller_name').'%');
                });
        }
        return $next($request);
    }
}
