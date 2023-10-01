<?php

namespace App\Http\Controllers;

use App\Http\Requests\productRateFormRequest;
use App\Http\Resources\RateResource;
use App\Http\traits\messages;
use App\Models\orders_items;
use App\Models\orders_items_rates;
use Illuminate\Http\Request;

class RateController extends Controller
{
    //

    public function rate_per_order($data){
        $order_items = orders_items::query()
            ->whereDoesntHave('rate')
            ->whereHas('order',function($e) use ($data){
                $e->where('user_id',auth()->id())->where('id',$data['order_id']);
            })->get();

        if(sizeof($order_items) > 0){
            foreach($order_items as $item){
                $data['user_id'] = auth()->id();
                $data['order_item_id'] = $item->id;
                orders_items_rates::query()->create($data);
            }

            return messages::success_output(trans('messages.rated_successfully'),$data);
        }else{
            return messages::error_output(trans('errors.please_order_this_product_to_rate_it'));
        }
    }

    public function rate_product_per_product($data){
        $order_item = orders_items::query()
            ->whereDoesntHave('rate')
            ->whereHas('order',function($e){
                $e->where('user_id',auth()->id());
            })
            ->where('product_id','=',$data['product_id'])->first();
        if($order_item != null){
            unset($data['product_id']);
            $data['user_id'] = auth()->id();
            $data['order_item_id'] = $order_item->id;
            $rate =  orders_items_rates::query()->with('user')->updateOrCreate([
                'id'=>$data['id'] ?? null,
            ],$data);
            return messages::success_output(trans('messages.rated_successfully'),RateResource::make($rate));
        }else{
            return messages::error_output(trans('errors.please_order_this_product_to_rate_it'));
        }
    }

    public function make(productRateFormRequest $request){
        $data = $request->validated();
        // in case rate product per product
        if(request()->has('order_id')){
            return $this->rate_per_order($data);
        }else {
            return $this->rate_product_per_product($data);
        }
    }
}
