<?php


namespace App\Http\traits\helpers_requests_api;


use App\Models\financial_reconciliations;
use App\Models\financial_reconciliations_profit_percentages;
use App\Models\orders;
use App\Models\payments;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait SellersStatisticsHelperApi
{
    public function get_statistics($time){
        $output  = financial_reconciliations::query()->selectRaw('sum(total_money - (total_money * admin_profit_percentage / 100)) as sellers_profit
        , sum(total_money * admin_profit_percentage / 100) as admin_profit')
            ->whereRaw($time)
            ->first();

        $pending_money_orders = payments::query()->whereHas('orders',function ($e){
            $e->whereRaw('financial_reconciliation_id is null');
        })->orWhereHas('custom_orders',function ($e){
            $e->whereRaw('financial_reconciliation_id is null');
        })
            ->whereRaw($time)
            ->sum('money');

        $sellers_count = User::query()->whereHas('role',function($e){
            $e->where('name','=','seller');
        })
            ->whereRaw($time)
            ->count();

        $percentage_admin = financial_reconciliations_profit_percentages::query()->where('from_who','=','admin')->first()->percentage;
        $output['pending_sellers_profit'] = ($pending_money_orders - ($pending_money_orders * $percentage_admin / 100));
        $output['sellers_count'] = $sellers_count;

        return $output;
    }


    public function all_statistics(){
        return $this->get_statistics("MONTH(".DB::raw('created_at').") = MONTH(CURRENT_DATE)");
    }
}
