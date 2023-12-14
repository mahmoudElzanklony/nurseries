<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\Resources\CancelOrderItemResource;
use App\Models\cancelled_orders_items;
use App\Models\custom_orders;
use App\Models\orders_items;
use App\Models\orders_items_features;
use App\Models\withdraw_money;

trait WithdrawMoneyHelperApi
{
    public function manage_data($data){
        foreach($data as $d){
            if($d->type == 'order'){
                $d->order_item = orders_items::query()->with('order',function($e){
                    $e->with(['seller.image','client.image']);
                })->find($d->order_item_id);
                $d->order_item->features = orders_items_features::query()->where('order_item_id','=',$d->order_item_id);
            }else{
                $d->order_item = custom_orders::query()->with(['user','reply.custom_order_seller.seller.image'])->find($d->order_item_id);
            }
        }
        return $data;
    }
    public function all_withdraw_money(){
        $itemsPaginated =  cancelled_orders_items::query()->with('images')->orderBy('id','DESC')->paginate(9);

        $itemsTransformed =  $this->manage_data($itemsPaginated->getCollection());
        $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsTransformed,
            $itemsPaginated->total(),
            $itemsPaginated->perPage(),
            $itemsPaginated->currentPage(), [
                'path' => \Request::url(),
                'query' => [
                    'page' => $itemsPaginated->currentPage()
                ]
            ]
        );
        return $itemsTransformedAndPaginated;
    }

    public function withdraw_product_money(){
        $itemsPaginated =  cancelled_orders_items::query()->with('images')
            ->where('type','=','order')
            ->whereHas('order_item',function($q){
                $q->whereHas('product',function($e){
                    $e->where('id','=',request('product_id'));
                });
            })->paginate(9);
        // features money
        $itemsTransformed =  $this->manage_data($itemsPaginated->getCollection());

        $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsTransformed,
            $itemsPaginated->total(),
            $itemsPaginated->perPage(),
            $itemsPaginated->currentPage(), [
                'path' => \Request::url(),
                'query' => [
                    'page' => $itemsPaginated->currentPage()
                ]
            ]
        );
    }
}
