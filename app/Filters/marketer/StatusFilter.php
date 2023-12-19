<?php


namespace App\Filters\marketer;

use Closure;
use App\Filters\FilterRequest;

class StatusFilter
{
    public function handle($request , Closure $next){
        if(!(request()->filled('status'))){
            return $next($request);
        }
        if(request('status') == 'accepted_rejected'){
            return $next($request)->whereRaw('status = "accepted" OR status = "rejected" ');
        }
        return $next($request)->where('status','=',request('status'));
    }
}
