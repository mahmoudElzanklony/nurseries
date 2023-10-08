<?php


namespace App\Actions;


use Carbon\Carbon;
use Illuminate\Support\Str;

class ManageTimeAlert
{

    public static function manage($time_number,$time_type , $last_time = null){
        if($last_time == null) {
            $now = Carbon::now();
        }else{
            $now = Carbon::create($last_time);
        }
        if ($time_type == 'minute') {
            $time = $now->addMinutes($time_number);
        } else if ($time_type == 'hour') {
            $time = $now->addHours($time_number);
        } else if ($time_type == 'day') {
            $time = $now->addDays($time_number);
        }  else if ($time_type == 'week') {
            $time = $now->addWeek($time_number);
        } else if ($time_type == 'month') {
            $time = $now->addMonths($time_number);
        } else if ($time_type == 'year') {
            $time = $now->addYears($time_number);
        }
        return $time;
    }

    public static function check_send_alert($time){
        $time = Carbon::create($time);
        $now = Carbon::now();
        if ($now->diffInMinutes($time) <= 3) {
            return true;
        }else{
            return false;
        }
    }

    public static function difference_between_two_times($current_time,$alert_time,$time_type){
        $current_time = Carbon::create($current_time);
        $alert_time = Carbon::create($alert_time);
        $time_type= Str::ucfirst($time_type);
        $time_type = 'diffIn'.$time_type.'s';
        return  $alert_time->{$time_type}($current_time);
    }
}
