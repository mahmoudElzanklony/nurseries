<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\traits\messages;
use App\Models\products;

trait ProductsHelperApi
{
    public function toggle_product(){
        $product = products::query()->find(request('id'));
        if($product->status == 1){
            $product->status = 0;
        }else{
            $product->status = 1;
        }
        $product->save();
        return messages::success_output(trans('messages.saved_successfully'));
    }
}
