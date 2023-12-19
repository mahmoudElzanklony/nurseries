<?php

namespace App\Http\Controllers;

use App\Actions\DefaultAddress;
use App\Actions\OrdersWithAllData;
use App\Actions\SendNotification;
use App\Filters\orders\MaxPriceFilter;
use App\Filters\orders\MinPriceFilter;
use App\Filters\orders\PaymentTypeFilter;
use App\Filters\orders\StatusOrderFilter;
use App\Filters\StartDateFilter;
use App\Filters\EndDateFilter;
use App\Http\Requests\ordersFormRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShipmentsInfo;
use App\Http\traits\messages;
use App\Models\orders;
use App\Models\orders_items;
use App\Models\orders_shipment_info;
use App\Models\reports;
use App\Models\taxes;
use App\Models\User;
use App\Models\user_addresses;
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
        /*$default_address = DefaultAddress::get();
        // check if user has no default address for delivery order
        if($default_address == null){
            return messages::error_output(trans('errors.no_default_address'));
        }*/
        $default_address = user_addresses::query()->find(request('address_id'));

        $order_repo = new OrderRepository($default_address);
        $seller = User::query()->find(request('seller_id'));
        // check if this of any these products any one that has no delivery way to default client address
        $check_err_delivery = $order_repo->check_delivery_products($data['items']);
       // if($check_err_delivery['error'] > 0){

        if(false){
            return messages::error_output(trans('keywords.seller').' ( '.$seller->username.' ) '.trans('keywords.dont_support_delivery_product').' ( '.$check_err_delivery['product_name'].' ) '.trans('keywords.to_default_address'),401);
        }
        if($order_repo->validate_payment_info($data['payment_data'])['status'] == true){
            // the visa is okay now
            $result = $order_repo->init_order($data)->order_items($data['items']);
            if($result != null) {
                $result = json_decode($result->content(), true);
                if (array_key_exists('status', $result) && $result['status'] != 200) {
                    return messages::error_output($result['errors']);
                }
            }
            DB::commit();
            $order_repo->order->has_coupon == "0" ? false:true;
            $order_repo->order->seller_profit = false;
            return messages::success_output(trans('messages.order_done_successfully'),$order_repo->order);
        }else{
            return messages::error_output('بيانات الفيزا الخاصه بك خاطئة يرجي مراجعتها من فضلك');
        }

    }

    public function all_orders(){
        $orders = OrdersWithAllData::get();
        if(request()->filled('id')){
            $output = OrdersWithAllData::get()->with(['seller.commercial_info'])->findOrFail(request('id'));
            return OrderResource::make($output);
        }
        $data = app(Pipeline::class)
            ->send($orders)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
                MinPriceFilter::class,
                MaxPriceFilter::class,
                StatusOrderFilter::class
            ])
            ->thenReturn()
            ->orderBy('id','DESC')
            ->paginate(10);
        return OrderResource::collection($data);
    }

    public function update_status(ordersFormRequest $request){
        $order = orders::query()->where('id','=',request('id'))->first();
        if($order != null){
            $status = request('status');
            $shipment = orders_shipment_info::query()->create([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'content' => $status
            ]);

            return messages::success_output(trans('messages.operation_saved_successfully'),OrderShipmentsInfo::make($shipment));
            /*if($this->validate_update_order($status) == true) {

            }else{
                return messages::error_output('error in status value you sent  of this order');
            }*/
        }
    }

    public function validate_update_order($status){
        $available_statues = ['prepared','delivered','completed'];
        $user = User::query()->with('role')->find(auth()->id());
        if(($user->role->name == 'client'|| $user->role->name == 'company') && $status != 'cancelled') {
            return false;
        }else if($user->role->name == 'seller'){
            if(!(in_array($status,$available_statues))){
                return false;
            }
        }
        return true;
    }

}
