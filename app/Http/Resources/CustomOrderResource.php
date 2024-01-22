<?php

namespace App\Http\Resources;

use App\Actions\GetHighDeliveryDays;
use App\Models\cancelled_orders_items;
use App\Models\custom_orders_sellers;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if($this->status != 'pending') {
            try{
                $accepted_seller_from_client = custom_orders_sellers::query()
                    ->with('seller')->with('reply')
                    ->where('custom_order_id', '=', $this->id)
                    ->where('status', '=', 'accepted')
                    ->where('client_reply', '=', 'accepted')
                    ->first();
            }catch (\Throwable $e){
                $accepted_seller_from_client = null;
            }
        }else{
            $accepted_seller_from_client = null;
        }

        return [
           'id'=>$this->id,
           'client'=>UserResource::make($this->user),
           'name'=>$this->name,
           'status'=>$this->status,
           'selected_seller'=>$this->when($this->status != 'pending' && $accepted_seller_from_client != null , function () use ($accepted_seller_from_client){
               return UserResource::make($accepted_seller_from_client->seller);
           }),
           'selected_products'=>SelectedProductsResource::collection($this->whenLoaded('selected_products')),
           'delivery_data'=>$this->when($this->whenLoaded('selected_products'),function (){
               return $this->selected_products[0];
           }),
           'address'=>$this->when(true,function (){
                if($this->address != null){
                    return UserAddressesResource::make($this->address);
                }else{
                    return null;
                }
            }),
           'ar_status'=>trans('keywords.'.$this->status),
           'accepted_date'=>$this->when(true,function() use ($accepted_seller_from_client ){
                // fix accepted date
                if($this->status != 'pending' && $accepted_seller_from_client != null){
                    return $accepted_seller_from_client->updated_at;
                }else{
                    return null;
                }
            }),
           'delivery_date'=>$this->when(true ,function() use ($accepted_seller_from_client){

                if($this->status != 'pending' && $accepted_seller_from_client != null){
                    $delivery = GetHighDeliveryDays::get($accepted_seller_from_client->reply);
                    return Carbon::parse($accepted_seller_from_client->updated_at)->addDays($delivery['days_delivery']);
                }else{
                    return null;
                }
            }),
            'cancelled'=>$this->when(auth()->user()->role->name == 'admin',function() {
                return cancelled_orders_items::query()
                    ->where('order_item_id','=',$this->id)
                    ->where('type','=','custom-order')->first() != null ? true:false;
            }),
           'images'=>ImagesResource::collection($this->images),
           'pending_alerts'=>$this->when(auth()->check() && isset($this->has_pending),function(){
               return CustomOrderSellerResource::collection($this->whenLoaded('pending_alerts'));
           }),
           'accepted_alerts'=>CustomOrderSellerResource::collection($this->whenLoaded('accepted_alerts')),
           'rejected_alerts'=>CustomOrderSellerResource::collection($this->whenLoaded('rejected_alerts')),
           'payment'=>PaymentResource::make($this->whenLoaded('payment')),
           'created_at'=>$this->created_at,
        ];
    }
}
