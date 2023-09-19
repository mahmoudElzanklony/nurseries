<?php


namespace App\Actions;


use App\Models\operations;
use App\Services\DB_connections;
use Carbon\Carbon;

class ManageTransactionsAction
{

    public static function manage($type = 'create' , $operation){
        $difference = 0;
        $status = false;
        $now = Carbon::now();
        if($type == 'create') {
            if ($operation->last_time_create == null) {
                $status = true;
            } else if ($operation->last_time_create != null) {
                $time = Carbon::create($operation->last_time_create);
                $operation_period_obj = $operation->period
                    ->first(function ($e) use ($type) {
                        return $e->save_type == $type;
                    });
                if ($operation_period_obj != null) {
                    if ($operation_period_obj->type == 'minute') {
                        $time = $time->addMinutes($operation_period_obj->period);
                        $difference = $now->diffInMinutes($time);
                    } else if ($operation_period_obj->type == 'hour') {
                        $time = $time->addHours($operation_period_obj->period);
                        $difference = $now->diffInHours($time);
                    } else if ($operation_period_obj->type == 'month') {
                        $time = $time->addMonths($operation_period_obj->period);
                        $difference = $now->diffInMonths($time);
                    } else if ($operation_period_obj-> type == 'year') {
                        $time = $time->addYears($operation_period_obj->period);
                        $difference = $now->diffInYears($time);
                    }
                    if ($difference >= $operation_period_obj->period) {
                        $status = true;
                    }
                }
            }
        }else{
            if ($operation->last_time_update == null) {
                $status = true;
            } else if ($operation->last_time_update != null) {
                $time = Carbon::create($operation->last_time_update);
                $operation_period_obj = $operation->period
                    ->first(function ($e) use ($type) {
                        return $e->save_type == $type;
                    });
                if ($operation_period_obj != null) {
                    if ($operation_period_obj->type == 'minute') {
                        $time = $time->addMinutes($operation_period_obj->period);
                        $difference = $now->diffInMinutes($time);
                    } else if ($operation_period_obj->type == 'hour') {
                        $time = $time->addHours($operation_period_obj->period);
                        $difference = $now->diffInHours($time);
                    } else if ($operation_period_obj->type == 'month') {
                        $time = $time->addMonths($operation_period_obj->period);
                        $difference = $now->diffInMonths($time);
                    } else if ($operation_period_obj-- > type == 'year') {
                        $time = $time->addYears($operation_period_obj->period);
                        $difference = $now->diffInYears($time);
                    }
                    if ($difference >= $operation_period_obj->period) {
                        $status = true;
                    }
                }
            }
        }
        return $status;
    }

}
