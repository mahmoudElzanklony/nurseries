<?php

namespace App\Http\Controllers;

use App\Actions\OrdersWithAllData;
use App\Actions\SendNotification;
use App\Filters\orders\PaymentTypeFilter;
use App\Filters\StartDateFilter;
use App\Filters\EndDateFilter;
use App\Http\Requests\ordersFormRequest;
use App\Http\Resources\OrderResource;
use App\Http\traits\messages;
use App\Models\orders;
use App\Models\orders_items;
use App\Models\orders_shipment_info;
use App\Models\reports;
use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    //

    public function make_order(ordersFormRequest $request){
        DB::beginTransaction();
        $data = $request->validated();
        $order_repo = new OrderRepository();
        if($order_repo->validate_payment_info($data)['status'] == true){
            // the visa is okay now
            $result = $order_repo->init_order($data)->order_items($data['items']);
            if($result != null) {
                $result = json_decode($result->content(), true);
                if (array_key_exists('status', $result) && $result['status'] != 200) {
                    return messages::error_output($result['errors']);
                }
            }
            DB::commit();
            return messages::success_output(trans('messages.order_done_successfully'),$order_repo->order);
        }else{
            return messages::error_output($order_repo->validate_payment_info($data));
        }

    }

    public function all_orders(){
        $orders = OrdersWithAllData::get();

        $data = app(Pipeline::class)
            ->send($orders)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
            ])
            ->thenReturn()
            ->orderBy('id','DESC')
            ->get();
        return OrderResource::collection($data);
    }

    public function update_status(ordersFormRequest $request){
        $order = orders::query()->where('id','=',request('id'))->first();
        if($order != null){
            $status = request('status');
            if($this->validate_update_order($status) == true) {
                orders_shipment_info::query()->create([
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                    'content' => $status
                ]);
                return messages::success_output(messages::success_output('messages.operation_saved_successfully'));
            }else{
                return messages::error_output('error in status value you sent  of this order');
            }
        }
    }

    public function validate_update_order($status){
        $available_statues = ['shipped','delivered'];
        $user = User::query()->with('role')->find(auth()->id());
        if($user->role->name == 'client' && $status != 'cancelled') {
            return false;
        }else if($user->role->name == 'seller'){
            if(!(in_array($status,$available_statues))){
                return false;
            }
        }
        return true;
    }

}
