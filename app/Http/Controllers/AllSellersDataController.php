<?php

namespace App\Http\Controllers;

use App\Actions\CustomOrdersWithAllData;
use App\Actions\RepliesSellersWithAllData;
use App\Filters\custom_orders\SellerNameFilter;

use App\Filters\EndDateFilter;
use App\Filters\marketer\StatusFilter;
use App\Filters\UsernameFilter;
use App\Filters\StartDateFilter;
use App\Http\Resources\CustomOrderResource;
use App\Http\Resources\CustomOrderSellerResource;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class AllSellersDataController extends Controller
{
    //
    public function index(){
        $users = User::query()->has('commercial_info')->has('store_info')->whereHas('role',function($e){
            $e->where('name','=','seller');
        })->orderBy('id','DESC');


        if(auth()->user()->role->name == 'admin') {
            $output = app(Pipeline::class)
                ->send($users)
                ->through([
                    UsernameFilter::class
                ])
                ->thenReturn()
                ->get();
        }else{
            $output = app(Pipeline::class)
                ->send($users)
                ->through([
                    UsernameFilter::class
                ])->thenReturn()->get();
        }
        return UserResource::collection($output);

    }


    public function replies_custom_orders(){
        $data = RepliesSellersWithAllData::get()->whereHas('reply',function($e){
            $e->where('client_reply','=','pending');
        });
        if(request()->has('id')){
            $result = RepliesSellersWithAllData::get();
            if($result != null) {
                return CustomOrderSellerResource::make(RepliesSellersWithAllData::get()->find(request('id')));
            }else{
                return messages::error_output('لا يوجد ردود');
            }
        }
        $output = app(Pipeline::class)
            ->send($data)
            ->through([
                StatusFilter::class,
                \App\Filters\custom_orders\UsernameFilter::class,
                StartDateFilter::class,
                EndDateFilter::class,

            ])
            ->thenReturn()
            ->paginate(10);
        return CustomOrderSellerResource::collection($output);

    }
}
