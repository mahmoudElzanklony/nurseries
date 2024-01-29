<?php

namespace App\Http\Controllers;

use App\Actions\DefaultAddress;
use App\Actions\OrdersWithAllData;
use App\Actions\ProductWithAllData;
use App\Actions\SendNotification;
use App\Actions\UserCouponModal;
use App\Filters\orders\MaxPriceFilter;
use App\Filters\orders\MinPriceFilter;
use App\Filters\orders\PaymentTypeFilter;
use App\Filters\orders\StatusOrderFilter;
use App\Filters\StartDateFilter;
use App\Filters\EndDateFilter;
use App\Http\Requests\ordersFormRequest;
use App\Http\Resources\CheckCouponResource;
use App\Http\Resources\OrderCouponResource;
use App\Http\Resources\OrderItemsResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderShipmentsInfo;
use App\Http\traits\messages;
use App\Models\orders;
use App\Models\orders_items;
use App\Models\orders_shipment_info;
use App\Models\products;
use App\Models\reports;
use App\Models\taxes;
use App\Models\User;
use App\Models\user_addresses;
use App\Models\users_coupons;
use App\Repositories\CouponRepository;
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

        $default_address = user_addresses::query()->find(request('address_id'));

        $order_repo = new OrderRepository($default_address);
        $seller = User::query()->find(request('seller_id'));
        // check if this of any these products any one that has no delivery way to default client address
        $check_err_delivery = $order_repo->check_delivery_products($data['items']);
        if($check_err_delivery['error'] > 0){
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

            return messages::success_output(trans('messages.order_done_successfully'),OrderResource::make(OrdersWithAllData::get()->find($order_repo->order->id)));
        }else{
            return messages::error_output('بيانات الفيزا الخاصه بك خاطئة يرجي مراجعتها من فضلك');
        }

    }

    public function check_coupon(ordersFormRequest $request)
    {
        DB::beginTransaction();
        $data = $request->validated();

        $default_address = user_addresses::query()->find(request('address_id'));

        $order_repo = new OrderRepository($default_address);
        $seller = User::query()->find(request('seller_id'));
        // check if this of any these products any one that has no delivery way to default client address
        $check_err_delivery = $order_repo->check_delivery_products($data['items']);
        if($check_err_delivery['error'] > 0){
            return messages::error_output(trans('keywords.seller').' ( '.$seller->username.' ) '.trans('keywords.dont_support_delivery_product').' ( '.$check_err_delivery['product_name'].' ) '.trans('keywords.to_default_address'),401);
        }
        if(true){
            // the visa is okay now

            $coupon_repos = new CouponRepository();
            $products = collect($data['items'])->map(function ($e){
               return ProductWithAllData::get()->find($e['product_id']);
            });

            $coupon_repos->validate_exist($data['has_coupon'],$products->map(fn ($e)=> $e['id'] ));
            if($coupon_repos->coupon == null){
                return messages::error_output('عذرا بيانات الكوبون خاطئه يرجي المحاولة مرة اخري');
            }
            if($coupon_repos->error != null){
                return messages::error_output($coupon_repos->error);
            }
            $final = [];
            foreach(request('items') as $item){
                $obj = new orders_items();
                $obj->product_id = $item['product_id'];
                $obj->product = collect($products)->first(function ($e) use ($item){
                    return $e->id == $item['product_id'];
                });
                $obj->quantity = $item['quantity'];
                $orderRepo = new OrderRepository($default_address);
                $orderRepo->set_coupon($coupon_repos->coupon);
                $whole_price = $orderRepo->wholesale_price_item($obj->product,$item['quantity']); // for example :20
                // check if there is discount at this date
                $discount = $orderRepo->discount_per_product($obj->product);
                // handle final price
                //echo 'quantity ==>'.$item['quantity'] .'<br>';
                $final_price = $orderRepo->handle_final_price($obj->product,$whole_price,$discount,'product',$item['quantity']);
                // check if this product applied for coupon

                if($orderRepo->validate_product_for_coupon($item['product_id']) == true){
                    $total_price_before_apply_coupon = $final_price;
                    $coupon_value_cash = $final_price * $coupon_repos->coupon->discount / 100;
                    $final_price -= ($final_price * $coupon_repos->coupon->discount / 100);
                    $obj->coupon = new users_coupons();
                    $obj->coupon->total_price_before_apply = $total_price_before_apply_coupon;
                    $obj->coupon->coupon_value = $coupon_value_cash;
                    $obj->coupon->coupon = $coupon_repos->coupon;
                }
                $obj->price = $final_price;
                // check for apply coupon
                array_push($final,$obj);
            }

            $last_final = [
                'data'=>$final,
                'coupon'=> $coupon_repos->coupon
            ];

            return OrderCouponResource::collection($last_final);
        }else{
            return messages::error_output('بيانات الفيزا الخاصه بك خاطئة يرجي مراجعتها من فضلك');
        }
    }

    public function all_orders(){
        if(request()->filled('id')){
            $output = OrdersWithAllData::get()->with(['seller.commercial_info'])->findOrFail(request('id'));
            return OrderResource::make($output);
        }
        $orders = OrdersWithAllData::get();
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
                'type'=>'order',
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
