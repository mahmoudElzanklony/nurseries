<?php

namespace App\Http\Resources;

use App\Actions\SellerRateAVG;
use App\Models\cancelled_orders_items;
use App\Models\coupons;
use App\Models\financial_reconciliations;
use App\Models\orders_items;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $payment;
    public function toArray($request)
    {
        $seller_avg_rate = SellerRateAVG::get($this->seller->id);
        return [
          'id'=>$this->id,
          'payment_method'=>$this->payment_method,
          'has_coupon'=>$this->has_coupon == 0 ? false:true,
          'coupon'=>$this->when($this->has_coupon == 1 , function (){
              $item =  orders_items::query()->where('order_id','=',$this->id)->has('coupon')->with('coupon')->first();
              if($item != null){
                  $coupon = coupons::query()->withTrash($item->couponable_id);
                  dd($coupon);
                  if($coupon != null) {
                      return CouponRessource::make($coupon);
                  }
                  return null;
              }
              return null;
          }),
          'seller_profit'=>$this->seller_profit == 0 ? false:true,
          'items_price'=>round(doubleval($this->total_items),2),
          'address'=>$this->when(true,function (){
              if($this->address != null){
                  if($this->address->location != null){
                      return UserAddressesResource::make($this->address->location);
                  }
                  return $this->address;
              }else{
                  return null;
              }
          }),
          'delivery'=>OrderDeliveryResource::make($this->whenLoaded('address')),
          'tax_percentage'=>$this->when($this->whenLoaded('payment'),function(){
              return $this->payment->tax;
          }),
          'tax_value'=>$this->when($this->whenLoaded('payment'),function(){
                return ($this->payment->money * $this->payment->tax / 100); // 230
          }),
          'cancelled'=>$this->when(auth()->user()->role->name == 'admin',function(){
                return cancelled_orders_items::query()
                    ->where('order_item_id','=',$this->id)
                    ->where('type','=','order')->first() != null ? true:false;
          }),
          'avg_rates_seller'=>round(($seller_avg_rate['avg_services']+$seller_avg_rate['avg_delivery'])/2,2),
          'client'=>UserResource::make($this->whenLoaded('client')),
          'seller'=>UserResource::make($this->whenLoaded('seller')),
          //'payment'=>PaymentResource::make($this->whenLoaded('payment')),
          'payment'=>$this->when($this->whenLoaded('payment'),function(){
              $paypment_with_tax = $this->payment->money;
              $tax_percen = $this->payment->tax;
              $total_money_without_tax = $paypment_with_tax / (1+$tax_percen/100);
              $tax_value = $paypment_with_tax - $total_money_without_tax;
              return [
                  'tax_value'=>round($tax_value, 2),
                  'tax_percentage'=>$tax_percen,
                  'total_money_without_tax'=>round($total_money_without_tax, 2),
                  'money'=>round($paypment_with_tax, 2)
              ];
          }),
          'items'=>OrderItemsResource::collection($this->whenLoaded('items')),
          'shipments_info'=>OrderShipmentsInfo::collection($this->whenLoaded('shipments_info')),
          'financial'=>$this->when($this->financial_reconciliation_id != null && auth()->user()->role->name == 'admin',function(){
              return FinancialReconciliationResource::make(financial_reconciliations::query()->find($this->financial_reconciliation_id));
          }) ,
          'status'=>isset($this->shipments_info) && sizeof($this->shipments_info)  > 0 ? $this->shipments_info[sizeof($this->shipments_info) - 1]->content:'pending',
          'created_at'=>$this->created_at,

        ];
    }
}
