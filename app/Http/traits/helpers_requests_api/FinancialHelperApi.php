<?php


namespace App\Http\traits\helpers_requests_api;


use App\Filters\EndDateFilter;
use App\Filters\StartDateFilter;
use App\Filters\UsernameFilter;
use App\Filters\users\RoleNameFilter;
use App\Http\Resources\CustomOrderResource;
use App\Http\Resources\CustomOrderSellerResource;
use App\Http\Resources\FinancialReconciliationResource;
use App\Http\Resources\OrderItemsResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\custom_orders;
use App\Models\custom_orders_sellers;
use App\Models\financial_reconciliations;
use App\Models\financial_reconciliations_profit_percentages;
use App\Models\orders;
use App\Models\orders_items;
use App\Models\orders_items_features;
use App\Models\rejected_financial_orders;
use App\Models\User;
use App\Repositories\FinancialReconciliationsRepository;
use Illuminate\Pipeline\Pipeline;

trait FinancialHelperApi
{

    public function quick_financial_statistics(){
        $no_actions_per_orders  = orders::query()->whereRaw('financial_reconciliation_id is null')->count();
        $no_actions_per_custom_orders = custom_orders::query()->whereRaw('financial_reconciliation_id is null')->count();
        $data = financial_reconciliations::query();
        $pending = $data->where('status','=','pending')->count();
        $accepted = $data->where('status','=','accepted')->count();
        $output = [
            'no_actions'=> $no_actions_per_orders + $no_actions_per_custom_orders,
            'pending'=>$pending,
            'accepted'=>$accepted
        ];
        return $output;
    }

    public function financial_data(){
        $type = request('type') ?? '';
        $status = request('status') ?? '';
        if($status == 'no_action'){

            return $this->pending_orders_data();
        }
        $output =  financial_reconciliations::query()->with('seller.bank_info')
            ->when($type != '',function($q) use ($type){
                $q->whereHas('user.role',function($e) use ($type){
                   $e->where('name','=',$type);
                });
             })
            ->when($status != '',function($q) use ($status){
                $q->where('status','=',$status);
            })
             ->orderBy('id','DESC');
        $final = app(Pipeline::class)
            ->send($output)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,
            ])
            ->thenReturn()
            ->paginate(10);
        return FinancialReconciliationResource::collection($final);
    }

    public function pending_orders_data(){
        $output  = [];
        $financial_percentage = financial_reconciliations_profit_percentages::query()->where('from_who','=','admin')->first();

        $all_sellers = User::query()->whereHas('role',function($e){
            $e->where('name','=','seller');
        })->get();

        foreach($all_sellers as  $seller){
            $money = 0;
            $orders = orders::query()->with('items.cancelled')->whereHas('last_shipment_info',function ($e){
                    $e->where('content','=','completed');
                })
                ->whereRaw('financial_reconciliation_id is null and seller_id = '.$seller->id)
                ->with('payment:paymentable_id,money')->get();

            foreach($orders as $o){
                $cancel = 0;
                foreach($o->items as $item){
                    if($item->cancelled != null && $item->cancelled->type == 'order'){
                        $price = $item->quantity * $item->price;
                        $features = orders_items_features::query()->where('order_item_id','=',$item->id)->get();
                        foreach($features as $feature){
                            $price += $feature->price;
                        }
                        $cancel += $price;
                    }
                }
                if(isset($o->payment) && $o->payment != null) {
                    $money += $o->payment->money;
                    echo 'money now ======>'.$money;
                    $money -= $cancel;
                }

            }
            $custom  = custom_orders_sellers::query()
                ->whereHas('order',function($e){
                    $e->whereDoesntHave('canceled');
                })
                ->where('seller_id',$seller->id)->whereHas('order',function ($o){
                    $o->whereRaw('financial_reconciliation_id is null');
                })->with(['order.payment'])->whereHas('reply',function($q){
                    $q->where('client_reply','=','accepted');
                })->orderBy('id','DESC')->get();

            $result = [
                'seller'=>UserResource::make(User::query()->with('bank_info')->find($seller->id)),
                'total_money'=>$money,
                'total_money_per_seller'=>$money - ($money * $financial_percentage->percentage / 100),
                'admin_profit_percentage'=>$financial_percentage->percentage
            ];
            if($result['total_money'] > 0) {
                array_push($output, $result);
            }
        }

        return $output;
    }

    public function financial_details(){
        if(request()->filled('financial_reconciliation_id')) {
            $financial_info = financial_reconciliations::query()->find(request('financial_reconciliation_id'));
            if($financial_info != null && $financial_info->status == 'rejected'){

                $rejected_orders_ids = rejected_financial_orders::query()
                    ->where('financial_reconciliation_id','=',$financial_info->id)
                    ->where('order_type','=','order')->get()->map(function($e){
                        return $e->order_id;
                    })->toArray();
                $products = orders_items::query()->with('cancelled')->whereHas('order', function ($e) use ($rejected_orders_ids){
                    $e->whereIn('id',$rejected_orders_ids);
                })->with('product', function ($e) {
                    $e->with(['problems','images','features.feature.image','answers'=>function($e){
                        $e->with('question');
                    }]);
                })->get();
                $rejected_custom_ids = rejected_financial_orders::query()
                    ->where('financial_reconciliation_id','=',$financial_info->id)
                    ->where('order_type','=','custom_order')->get()->map(function($e){
                        return $e->order_id;
                    })->toArray();
                $custom = custom_orders::query()->with('cancelled')->with('images')->with('payment')
                    ->whereIn('id',$rejected_custom_ids)
                    ->get();
            }else {
                $products = orders_items::query()->with('cancelled')->with('order')->whereHas('order', function ($e) {
                    $e->where('financial_reconciliation_id', '=', request('financial_reconciliation_id'));
                })->with('product', function ($e) {
                    $e->with(['problems', 'images', 'features.feature.image', 'answers' => function ($e) {
                        $e->with('question');
                    }]);
                })->get();
                $custom = custom_orders::query()->with('cancelled')->with('images')->with('payment')
                    ->where('financial_reconciliation_id', '=', request('financial_reconciliation_id'))->get();
            }
            return [
              'orders'=>OrderItemsResource::collection($products),
              'custom_orders'=>CustomOrderResource::collection($custom)
            ];
        }else if(request()->filled('seller_id')){
            // send seller id for orders that has no action
            $financil_repo = new FinancialReconciliationsRepository();
            $orders = $financil_repo->get_orders_to_be_financial(false,request('seller_id'),null , true);

           /* $orders =  orders_items::query()->whereHas('order', function ($e) {
                $e->where('financial_reconciliation_id', '=', request('financial_reconciliation_id'))->where('seller_id','=',request('seller_id'));
            })->with('product', function ($e) {
                $e->with(['problems','images','features.feature.image','answers'=>function($e){
                    $e->with('question');
                }]);
            })->get();

            $custom  = custom_orders_sellers::query()
                ->where('seller_id',request('seller_id'))
                ->whereHas('order',function ($o){
                    $o->whereRaw('financial_reconciliation_id is null');
                })
                ->with(['order.payment'])
                ->whereHas('reply',function($q){
                    $q->where('client_reply','=','accepted');
                })->orderBy('id','DESC')->get();*/
            return [
                'orders'=>OrderResource::collection($orders['orders']),
                'custom_orders'=>CustomOrderResource::collection($orders['custom_orders'])
            ];

        }
    }

    public function accept_send_money(){
        if(request()->filled('seller_id')){
            $financil_repo = new FinancialReconciliationsRepository();
            $orders = $financil_repo->get_orders_to_be_financial(false,request('seller_id'));
            if(sizeof($orders['orders']) > 0 || sizeof($orders['custom_orders']) > 0){
                $financil_repo->store_data($orders['orders'],$orders['custom_orders'],null,request('seller_id'));
                financial_reconciliations::query()->find($financil_repo->financial_obj->id)->update([
                   'status'=>'completed'
                ]);
            }
        }else{
            if(request()->filled('reject') && request()->filled('financial_reconciliation_id')){
                financial_reconciliations::query()->find(request('financial_reconciliation_id'))->update([
                    'status'=>'rejected',
                    'note'=>request('note') ?? null
                ]);
                $rejected_orders = orders::query()->where('financial_reconciliation_id','=',request('financial_reconciliation_id'))->get();
                foreach($rejected_orders as $order){
                    rejected_financial_orders::query()->create([
                        'financial_reconciliation_id'=>request('financial_reconciliation_id'),
                        'order_id'=>$order->id,
                        'order_type'=>'order',
                    ]);
                    $order->financial_reconciliation_id = null;
                    $order->save();
                }
                $rejected_custom_orders = custom_orders::query()->where('financial_reconciliation_id','=',request('financial_reconciliation_id'))->get();
                foreach($rejected_custom_orders as $order){
                    rejected_financial_orders::query()->create([
                        'financial_reconciliation_id'=>request('financial_reconciliation_id'),
                        'order_id'=>$order->id,
                        'order_type'=>'custom_order',
                    ]);
                    $order->financial_reconciliation_id = null;
                    $order->save();
                }

            }else {
                if (request()->filled('financial_reconciliation_id')) {
                    financial_reconciliations::query()->find(request('financial_reconciliation_id'))->update([
                        'status'=>'completed'
                    ]);

                }else{
                    return messages::error_output('لا يوجد اي اموال لهذا الشخص لكي يتم ارسالها او ان الطلبات لم يتم ايصالها للعميل بعد');
                }
            }
        }
        return messages::success_output(trans('messages.saved_successfully'));
    }
}
