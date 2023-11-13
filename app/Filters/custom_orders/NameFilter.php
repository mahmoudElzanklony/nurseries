<?php


namespace App\Filters\custom_orders;
use Closure;

class NameFilter
{
    public function handle($request, Closure $next){
        if(request()->has('name')){
            return $next($request)
                ->where('name','LIKE','%'.request('name').'%');
        }
        return $next($request);
    }
}
