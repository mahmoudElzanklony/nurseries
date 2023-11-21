<?php


namespace App\Filters\financial;

use Closure;
class RoleTypeFilter
{
    public function handle($request , Closure $next){
        if(!(request()->filled('role'))){
            return $next($request);
        }
        return $next($request)->whereHas('user.role',function($e){
            $e->where('name','=',request('role'));
        });
    }
}
