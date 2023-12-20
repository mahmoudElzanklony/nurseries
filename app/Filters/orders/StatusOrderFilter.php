<?php


namespace App\Filters\orders;


use App\Filters\FilterRequest;
use Closure;
class StatusOrderFilter extends FilterRequest
{
    public function handle($request, Closure $next){
        if(request()->has('status')){
            if(request('status') == 'pending'){
                return $next($request)->doesntHave('shipments_info');
            }else {
                return $next($request)->whereHas('last_shipment_info', function ($e) {
                    $e->where('content', '=', request('status'));
                });
            }
        }
        return $next($request);
    }
}
