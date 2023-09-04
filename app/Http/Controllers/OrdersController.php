<?php

namespace App\Http\Controllers;

use App\Actions\SendNotification;
use App\Filters\orders\PaymentTypeFilter;
use App\Filters\StartDateFilter;
use App\Filters\EndDateFilter;
use App\Http\Resources\OrderResource;
use App\Http\traits\messages;
use App\Models\packages_orders;
use App\Models\reports;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    //
    public function __construct(){
        $this->middleware('CheckApiAuth');
    }
    public function client_orders(){
        $user = User::query()->with('role')->find(auth()->id());
        $orders = packages_orders::with('package','bank_modal','user')
            ->when($user->role->name == 'client',function ($e){
                $e->where('user_id','=',auth()->id());
            })->when($user->role->name != 'client',function ($q){
                $q->with('user');
            });

        // filter data
        $data = app(Pipeline::class)
            ->send($orders)
            ->through([
                PaymentTypeFilter::class,
                StartDateFilter::class,
                EndDateFilter::class,
            ])
            ->thenReturn()
            ->orderBy('id','DESC')
            ->get();
        return OrderResource::collection($data);
    }

    public function accept(){
        // accept package request order
        $order = packages_orders::query()
            ->with('package','bank_modal','user')
            ->find(request('id'));
        $order->status = 1;
        $order->save();
        // send notification
        SendNotification::to_any_one_else_admin($order->user_id,'تم قبول طلب الباقة '.$order->package->name.' الخاص بك '
            ,'profile/'.$order->user_id.'/report');
        // make report
        reports::query()->create([
          'user_id'=>auth()->id(),
          'info'=>'قام '.auth()->user()->username.' بقبول الباقة  '.$order->package->name.' للعميل '.$order->user->username,
          'type'=>'package(accept)'
        ]);
        return messages::success_output('',$order);
    }
}
