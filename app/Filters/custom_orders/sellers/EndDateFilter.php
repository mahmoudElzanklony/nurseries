<?php


namespace App\Filters\custom_orders\sellers;
use Closure;

class EndDateFilter
{
    public function handle($request, Closure $next){
        if(request()->filled('start_date') ){
            return $next($request)->whereHas('order',function($e){
                $e->where('created_at','<=', request()->input('created_at'));
            });
        }
        return $next($request);
    }
}
