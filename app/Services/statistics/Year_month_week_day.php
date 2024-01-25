<?php


namespace App\Services\statistics;


use App\Actions\SellerOrdersAndCustomOrdersAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use function OpenAI\ValueObjects\Transporter\data;

class Year_month_week_day
{
    public function __construct()
    {
        Carbon::today('Asia/Riyadh');
    }
    public  function get_profit($model,$table = null ,$column_sum,$time_time,$conditions = [] , $created_at = 'created_at',$func_name = 'sum'){

        $method = 'statistics_per_'.$time_time;
        return $this->$method($model,$table,$column_sum,$time_time,$conditions,$created_at,$func_name);

    }

    public  function statistics_per_year($model,$table,$column_sum,$time_time,$conditions = [] , $created_at = 'created_at',$func_name){
        $output = [];
        for($i = 0; $i < 12; $i++) {
            if($table != null){
                $query_data = $table;
            }else{
                $query_data = app($model)::get();
            }
            $month = Carbon::parse(date('Y').'-'.($i+1).'-01')->firstOfMonth()->addDay();
            $value = $query_data
                ->when(sizeof($conditions) > 0 , function($e) use ($conditions,$time_time,$i){
                    $e->where($conditions);
                })
                ->whereMonth($created_at,(string)($i + 1))
                ->whereYear($created_at,date('Y'))
                ->{$func_name}($column_sum);
            $output[$i] = ['placeholder'=>$month , 'value'=> floatval($value)];
        }
        return $output;
    }

    public  function statistics_per_month($model,$table,$column_sum,$time_time,$conditions = [] , $created_at = 'created_at',$func_name){


        $output = [];
        for($i = 0; $i < 4; $i++) {
            if($table != null){
                $query_data = $table;
            }else{
                $query_data = app($model)::get();
            }
            $week = Carbon::parse(date('Y').'-'.($i+1).'-01')->firstOfMonth()->addDay()->week($i + 1);

            $value = $query_data
                ->when(sizeof($conditions) > 0 && $table != null , function($e) use ($conditions,$time_time,$i){
                    $e->where($conditions);
                })
                ->whereRaw(DB::raw("DAY(".$created_at.")").' >= '.(7*$i).'  && DAY('.$created_at.') < '. (7+(7*$i)))
                ->whereYear($created_at,date('Y'))
                ->whereMonth($created_at,date('m'))
                ->{$func_name}($column_sum);
            $output[$i] = ['placeholder'=>$week , 'value'=> floatval($value) ] ;
        }
        return $output;
    }

    public  function statistics_per_week($model,$table,$column_sum,$time_time,$conditions = [] , $created_at = 'created_at',$func_name){


        $output = [];

        $currentDate = Carbon::now();
        Carbon::setWeekStartsAt(Carbon::SATURDAY);
        $currentWeek = $currentDate->startOfWeek();
        for($i = 1; $i <= 7 ; $i++) {
            if($table != null){
                $query_data = $table;
            }else{
                $query_data = app($model)::get();
            }
            //echo Carbon::parse($currentWeek->toDateString())->addDays($i)->toDateString()."<br>";
            echo $created_at;
            $value =  $query_data
                ->when(sizeof($conditions) > 0 && $table != null , function($e) use ($conditions,$time_time,$i){
                    $e->where($conditions);
                })
                ->whereRaw($created_at.' = '.(Carbon::parse($currentWeek->toDateString())->addDays($i)->toDateString()))
                ->whereYear($created_at,date('Y'))
                ->whereMonth($created_at,date('m'))
                ->{$func_name}($column_sum) ;
            $output[$i - 1] = ['placeholder'=>Carbon::parse($currentWeek->toDateString())->addDays($i) , 'value'=> floatval($value)] ;
        }
        return $output;
    }
    public  function statistics_per_day($model,$table,$column_sum,$time_time,$conditions = [] , $created_at = 'created_at',$func_name){


        if($table != null){
            $query_data = $table;
        }else{
            $query_data = app($model)::get();
        }

        $output = $query_data
            ->when(sizeof($conditions) > 0 && $table != null , function($e) use ($conditions,$time_time){
                $e->where($conditions);
            })
            ->whereRaw(DB::raw("DAY(".$created_at.")").' = '.date('d'))
            ->whereYear($created_at,date('Y'))
            ->whereMonth($created_at,date('m'))
            ->first();
        $result = [];
        if($output != null){
            $result[0] = ['placeholder'=>Carbon::parse(date('Y-m-d H:i:s')) , 'value'=> floatval($output->{$column_sum}) ];
        }else{
            $result[0] = ['placeholder'=>Carbon::now() , 'value'=> 0 ];
        }

        return $result;
    }





}
