<?php

namespace App\Actions;

class GetHighDeliveryDays
{
    public static function get($data,$status = '')
    {
        if($status == '') {
            return [
                'days_delivery' => $data->max('days_delivery'),
                'delivery_price' => $data->max('delivery_price'),
            ];
        }else{
            $new_data = collect($data)->map(function($e) use ($status){
                return $e[$status];
            });
            dd($data[0]['reply']);
            return [
                'days_delivery' => $new_data->max('days_delivery'),
                'delivery_price' => $new_data->max('delivery_price'),
            ];
        }
    }
}
