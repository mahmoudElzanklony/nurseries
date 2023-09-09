<?php

namespace App\Http\Controllers;

use App\Http\Requests\productRateFormRequest;
use App\Http\traits\messages;
use App\Models\orders_items;
use App\Models\orders_items_rates;
use Illuminate\Http\Request;

class RateController extends Controller
{
    //
    public function make(productRateFormRequest $request){
        $data = $request->validated();
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
            orders_items_rates::query()->updateOrCreate([
                'id'=>$data['id'] ?? null,
            ],$data);
            return messages::success_output(trans('messages.rated_successfully'),$data);
        }else{
            return messages::error_output(trans('errors.please_order_this_product_to_rate_it'));
        }
    }
}
