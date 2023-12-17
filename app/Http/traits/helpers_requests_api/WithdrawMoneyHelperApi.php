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
                $d->order_item = orders_items::query()->with(['features','order',function($e){
                    $e->with(['seller.image','client.image']);
                }])->find($d->order_item_id);
                $d->order_item->features = orders_items_features::query()->where('order_item_id','=',$d->order_item_id);
            }else{
                $d->order_item = custom_orders::query()->with(['user','reply.custom_order_seller.seller.image'])->find($d->order_item_id);
            }
        }
        return $data;
    }
    public function all_withdraw_money(){
        $itemsPaginated =  cancelled_orders_items::query()->with(['images'])->orderBy('id','DESC')->paginate(9);

        $itemsTransformed =  $this->manage_data($itemsPaginated->getCollection());
        return $this->transfer_and_paginate($itemsTransformed,$itemsPaginated);
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


        return $this->transfer_and_paginate($itemsTransformed,$itemsPaginated);
    }

    public function transfer_and_paginate($new_transform,$orginal){
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $new_transform,
            $orginal->total(),
            $orginal->perPage(),
            $orginal->currentPage(), [
                'path' => \Request::url(),
                'query' => [
                    'page' => $orginal->currentPage()
                ]
            ]
        );

        $paginator->meta = [
            'total_pages' => $paginator->lastPage(),
            'current_page' => $paginator->currentPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'previous_page_url' => $paginator->previousPageUrl(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'links' => $paginator->toArray()['links']
            // Add any other meta data you need...
        ];

        return response()->json([
            'data' => $paginator->getCollection(),
            'meta' => $paginator->meta,
        ]);
    }
}
