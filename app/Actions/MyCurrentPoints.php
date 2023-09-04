<?php


namespace App\Actions;


use App\Models\packages_orders;
use Carbon\Carbon;

class MyCurrentPoints
{
    public static function get(){
        $total = 0;
        $points = packages_orders::query()
            ->where('user_id','=',auth()->id() ?? 6)
            ->where('status','=','1')
            ->where('current_points','>',0)
            ->with('package')->get();
        foreach($points as $p){
            if(Carbon::parse($p->created_at)->startOfDay()->diffInDays(Carbon::now()->startOfDay()) <= $p->package->expire_date){
                $total += $p->current_points;
            }
        }
        return $total;
    }

    public static function remove_from_my_acc($remove_number = 0){
        $current = $remove_number;
        $points = packages_orders::query()
            ->where('user_id','=',auth()->id() ?? 6)
            ->where('status','=','1')
            ->where('current_points','>',0)
            ->with('package')->get();
        foreach($points as $p){
            if(Carbon::parse($p->created_at)->startOfDay()->diffInDays(Carbon::now()->startOfDay()) <= $p->package->expire_date){
                if($current > $p->current_points){
                    $p->update([
                        'current_points'=>0
                    ]);
                    $current -= $p->current_points;
                }else{
                    $p->update([
                        'current_points'=>$p->current_points-$current
                    ]);
                    break;
                }

            }
        }
    }
}
