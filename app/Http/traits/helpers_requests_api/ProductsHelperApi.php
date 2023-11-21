<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\Resources\ProductProblemResource;
use App\Http\traits\messages;
use App\Models\products;
use App\Models\products_problems;

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

    public function update_product_problem_status(){
        $problem = products_problems::query()->find(request('id'));
        if($problem != null){
            $problem->status = request('status');
            $problem->save();
        }
        return messages::success_output(trans('messages.saved_successfully'),ProductProblemResource::make($problem));
    }
}
