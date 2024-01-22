<?php

namespace App\Actions;

class GetHighDeliveryDays
{
    public static function get($data)
    {
        return [
          'days_delivery'=>$data->max('days_delivery'),
          'delivery_price'=>$data->max('delivery_price'),
        ];
    }
}
