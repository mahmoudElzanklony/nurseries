<?php

namespace App\Http\Resources;

use App\Models\payments;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
     protected static $payment = null;
     public static function setPayment($order_id)
     {
         self::$payment = payments::query()
             ->where('paymentable_id','=',$order_id)
             ->where('paymentable_type','=','App\Models\orders')->first();
     }



    public function toArray($request)
    {
        if(self::$payment == null){
            self::setPayment($this->order_id);
        }
        return [
          'id'=>$this->id,
          'product'=>ProductResource::make($this->whenLoaded('product')),
          'rate'=>RateResource::make($this->whenLoaded('rate')),
          'features'=>OrderItemsFeaturesResource::collection($this->whenLoaded('features')),
          'quantity'=>$this->quantity,
          'prices_info'=>$this->when(self::$payment != null, function(){

              $tax_percen = self::$payment->tax;


             $all_price_with_tax = round($this->price, 2);
             $all_price_without_tax = round($this->price / (1+$tax_percen/100),2);
             $price_item_with_tax = round($this->price / $this->quantity, 2);
             $price_item_without_tax = round($all_price_without_tax / $this->quantity, 2);
             return [
               'all_price_with_tax'=>$all_price_with_tax,
               'all_price_without_tax'=>$all_price_without_tax,
               'price_item_with_tax'=>$price_item_with_tax,
               'price_item_without_tax'=>$price_item_without_tax,
             ];
          }),
         // 'total_price_with_tax'=>doubleval($this->price),
          'products_count'=>$this->when(isset($this->products_count),function(){
              return $this->products_count;
          }),
          'created_at'=>$this->created_at,

        ];
    }
}
