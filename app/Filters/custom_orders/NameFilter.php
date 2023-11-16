<?php


namespace App\Filters\custom_orders;
use Closure;

class NameFilter
{
    public function handle($request, Closure $next){
        if(request()->filled('name') && auth()->user()->role->name != 'seller'){
            return $next($request)
                ->where('name','LIKE','%'.request('name').'%');
        }else if(request()->filled('name') && auth()->user()->role->name == 'seller'){
            return $next($request)->whereHas('order',function($e){
                $e->where('name','LIKE','%'.request('name').'%');
            });

        }
        return $next($request);
    }
}
