<?php

namespace App\Http\Controllers;

use App\Actions\CustomOrdersWithAllData;
use App\Actions\RepliesSellersWithAllData;
use App\Filters\custom_orders\SellerNameFilter;

use App\Filters\UsernameFilter;
use App\Filters\StartDateFilter;
use App\Http\Resources\CustomOrderResource;
use App\Http\Resources\CustomOrderSellerResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class AllSellersDataController extends Controller
{
    //
    public function index(){
        $users = User::query()->whereHas('role',function($e){
            $e->where('name','=','seller');
        })->orderBy('id','DESC');

        $output = app(Pipeline::class)
            ->send($users)
            ->through([
               UsernameFilter::class
            ])
            ->thenReturn()
            ->paginate(10);

        return UserResource::collection($output);

    }


    public function replies_custom_orders(){
        $data = RepliesSellersWithAllData::get();
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StatusFilter::class,
                StartDateFilter::class,
                EndDateFilter::class,
            ])
            ->thenReturn()
            ->paginate(10);
        return CustomOrderSellerResource::collection($output);

    }
}
