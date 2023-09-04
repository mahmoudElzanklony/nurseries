<?php

namespace App\Http\Controllers;

use App\Filters\EndDateFilter;
use App\Filters\StartDateFilter;
use App\Filters\users\RoleIdFilter;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\traits\helpers_requests_api\PackagesHelperApi;
use App\Http\traits\helpers_requests_api\TicketsHelperApi;
use Illuminate\Pipeline\Pipeline;

class DashboardController extends Controller
{
    //
    use PackagesHelperApi,TicketsHelperApi;

    public function get_users(){
         $users = User::query()->with('role')
             ->orderBy('id','DESC');

        $output = app(Pipeline::class)
            ->send($users)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                RoleIdFilter::class
            ])
            ->thenReturn()
            ->paginate(15);
        return UserResource::collection($output);

    }


}
