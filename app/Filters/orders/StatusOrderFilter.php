<?php


namespace App\Filters\orders;


use App\Filters\FilterRequest;
use Closure;
class StatusOrderFilter extends FilterRequest
{
    public function handle($request, Closure $next){
        if(request()->has('status')){
            return $next($request)->whereHas('shipments_info',function($e){
                $e->where('content','=',request('status'));
            });
        }
        return $next($request);
    }
}
