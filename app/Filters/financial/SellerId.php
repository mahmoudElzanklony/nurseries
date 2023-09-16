<?php


namespace App\Filters\financial;

use Closure;
class SellerId
{
    public function handle($request , Closure $next){
         if(!(request()->has('seller_id'))){
             return $next($request);
         }
         return $next($request)->whereHas('orders',function ($e){
             $e->whereHas('products',function ($e){
                 $e->where('user_id','=',request('seller_id'));
             });
         });
    }
}
