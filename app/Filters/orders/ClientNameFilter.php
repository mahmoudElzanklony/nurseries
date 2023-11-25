<?php


namespace App\Filters\orders;

use Closure;
class ClientNameFilter
{
    public function handle($request , Closure $next){
        if(!(request()->filled('client_name'))){
            return $next($request);
        }
        return $next($request)->whereHas('client',function($e){
            $e->where('username','LIKE','%'.request('client_name').'%');
        });
    }
}
