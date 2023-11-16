<?php


namespace App\Filters\custom_orders\sellers;
use Closure;

class EndDateFilter
{
    public function handle($request, Closure $next){
        if(request()->filled('end_date') && request('end_date') != ''){
            return $next($request)->whereHas('order',function($e){
                $e->where('created_at','<=', request('end_date'));
            });
        }
        return $next($request);
    }
}
