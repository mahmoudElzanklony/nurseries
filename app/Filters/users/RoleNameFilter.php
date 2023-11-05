<?php


namespace App\Filters\users;

use Closure;
class RoleNameFilter
{
    public function handle($request , Closure $next){
        if(!(request()->filled('role_name'))){
            return $next($request);
        }
        return $next($request)->whereHas('role',function($e){
           $e->where('name','=',request('role_name'));
        });
    }
}
