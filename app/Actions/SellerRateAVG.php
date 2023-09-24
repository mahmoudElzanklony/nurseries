<?php


namespace App\Actions;


use App\Models\orders_items;

class SellerRateAVG
{
    public static function get($seller_id){
        $data = orders_items::query()->whereHas('product',function($e) use ($seller_id){
            $e->where('user_id','=',$seller_id);
        })->has('rate')->get();
        $total_rate_per_services = 0;
        $total_rate_per_delivery = 0;
        foreach($data as $d){
            $total_rate_per_services += $d->rate->rate_product_services;
            $total_rate_per_delivery += $d->rate->rate_product_delivery;
        }
        if((sizeof($data))){
            $avg_services = $total_rate_per_services / sizeof($data);
            $avg_delivery = $total_rate_per_delivery / sizeof($data);
        }
        return [
          'avg_services'=>$avg_services ?? 0,
          'avg_delivery'=>$avg_delivery ?? 0,
        ];
    }
}
