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
            'order_people_services'=>DB::select('SELECT users.id, users.username,users.email,
       COALESCE(article_count, 0) AS article_count,
       COALESCE(product_count, 0) AS product_count FROM users LEFT JOIN ( SELECT user_id, COUNT(DISTINCT id) AS article_count
       FROM articles GROUP BY user_id ) AS article_counts
           ON users.id = article_counts.user_id
           LEFT JOIN ( SELECT user_id, COUNT(DISTINCT id) AS product_count
           FROM products GROUP BY user_id ) AS product_counts ON users.id = product_counts.user_id
           WHERE users.role_id = 3
order BY product_count DESC')
        ];
        return messages::success_output('',$output);
    }
}
