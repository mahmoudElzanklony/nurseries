<?php


namespace App\Filters;
use Closure;

class UsernameFilter
{
    public function handle($request, Closure $next){
        if(request()->has('username')){
            return $next($request)
                ->where('username','LIKE','%'.request('username').'%');
        }
        return $next($request);
    }
}
