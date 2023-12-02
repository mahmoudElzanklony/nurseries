<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\traits\messages;
use App\Models\User;

trait DashboardHomeStatistics
{
    public function get_users_statistics(){
        $users = User::query();
        $output = [
            'sellers'=>$users->whereHas('role', function ($e) {
                 $e->where('name', '=', 'seller');
            })->count(),
        ];
        return messages::success_output('',$output);
    }
}
