<?php


namespace App\Filters\custom_orders\sellers;

use Closure;
use App\Filters\FilterRequest;

class StatusFilter
{
    public function handle($request , Closure $next){
        if(!(request()->filled('status'))){
            return $next($request);
        }
        return $next($request)->whereHas('order',function ($e){
           $e->where('status','=',request('status'));
        });
    }
}
