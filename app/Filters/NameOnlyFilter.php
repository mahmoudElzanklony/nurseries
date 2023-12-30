<?php


namespace App\Filters;
use Closure;

class NameOnlyFilter
{
    public function handle($request, Closure $next){
        if(request()->has('name')){
            return $next($request)
                ->where('name','LIKE','%'.request('name').'%');
        }
        return $next($request);
    }
}
