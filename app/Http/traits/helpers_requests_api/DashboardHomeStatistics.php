<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\traits\messages;
use App\Models\articles;
use App\Models\financial_reconciliations;
use App\Models\payments;
use App\Models\products;
use App\Models\User;
use App\Models\users_packages;
use Illuminate\Support\Facades\DB;

trait DashboardHomeStatistics
{
    public function get_users_statistics(){
        $money = financial_reconciliations::query()
            ->selectRaw('sum(total_money - (total_money  * admin_profit_percentage / 100)) as "seller_profit",sum(total_money  * admin_profit_percentage / 100)  as "admin_profit"')
            ->first();
        $output = [
            'sellers'=>User::query()->whereHas('role', function ($e) {
                 $e->where('name', '=', 'seller');
            })->count(),
            'clients'=>User::query()->whereHas('role', function ($e) {
                $e->where('name', '=', 'client');
            })->count(),
            'companies'=>User::query()->whereHas('role', function ($e) {
                $e->where('name', '=', 'company');
            })->count(),
            'subscriptions'=>users_packages::query()->where('expiration_date','>=',date('Y-m-d'))->count(),
            'products'=>products::query()->count(),
            'articles'=>articles::query()->count(),
            'money'=>[
                'clients_money'=>payments::query()->sum('money'),
                'sellers_profit'=>$money->seller_profit,
                'admin_profit'=>$money->admin_profit,
            ],
            'order_people_services'=>DB::table('users')
                ->leftJoin('images','images.imageable_id','=','users.id')
                ->leftJoin('products','products.user_id','=','users.id')
                ->leftJoin('articles','articles.user_id','=','users.id')
                ->selectRaw('users.id , users.username,images.name as image,count(articles.id) as total_articles,count(products.id) as total_products')
                ->whereRaw('users.role_id = 3')
                ->groupBy('users.id')
                ->paginate()
        ];
        return messages::success_output('',$output);
    }
}
