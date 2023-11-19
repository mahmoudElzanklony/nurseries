<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\User;
use App\Services\sellers\StatisticsService;
use App\Services\users\toggle_data;

trait SellersInfoHelperApi
{
    public function toggle_permission(){
        if(request()->filled('user_id')) {
            return toggle_data::toggle_article_permission(request('user_id'));
        }
    }

    public function about_seller(){
        if(request()->filled('user_id')) {
            $user = User::query()->with('article_permission')->with('bank_info')->find(request('user_id'));
            return messages::success_output('', StatisticsService::orders_money_products(request('user_id')),UserResource::make($user));
        }
    }
}
