<?php

namespace App\Http\Resources;

use App\Actions\SellerRateAVG;
use App\Models\custom_orders;
use App\Models\orders;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->role->name == 'seller') {
            $seller_avg_rate = SellerRateAVG::get($this->id);
        }

        return [
          'id'=>$this->id,
          'username'=>$this->username,
          'email'=>$this->email,
          'phone'=>$this->phone,
          'new_user'=>$this->new_user ?? false,
          'role'=>$this->whenLoaded('role'),
          'default_address'=>$this->when(isset($this->default_address),function (){
              return UserAddressesResource::make($this->default_address);
          }),
          'token'=>isset($this->token) ? $this->token : null,
          'article_permission'=>isset($this->article_permission) ? true:false,
          'avg_rates'=>isset($seller_avg_rate)  ?
                       round(($seller_avg_rate['avg_services']+$seller_avg_rate['avg_delivery'])/2,2) : null,
          'image'=>$this->image != null ? ImagesResource::make($this->image) : ['image'=>'users/default.png'],
          'products'=>ProductResource::collection($this->whenLoaded('products')),
          'client_visas'=>VisaBankResource::collection($this->whenLoaded('client_visas')),
          'bank_info'=>BankInfoResource::make($this->whenLoaded('bank_info')),
          'store_info'=>UserStoreInfoResource::make($this->whenLoaded('store_info')),
          'type'=>auth()->user()->store_info(),
          'complete_data'=>$this->when(auth()->check() && auth()->user()->role->name == 'seller',function($e){
             if(auth()->user()->store_info != null){
                 return true;
             }else{
                 return false;
             }
          }),
          'commercial_info'=>CommercialInfoResource::make($this->whenLoaded('commercial_info')),
          'products_count'=>$this->when(isset($this->products_count),function(){
              return $this->products_count;
          }),
          'orders_count'=>$this->when(auth()->check() && auth()->user()->role->name == 'admin',function(){
                $orders = orders::query()->where('user_id','=',$this->id)->count();
                $custom = custom_orders::query()->where('user_id','=',$this->id)->count();
                return $orders + $custom;
          }),
          'block'=>$this->block,
          'articles'=>ProductResource::collection($this->whenLoaded('articles')),
          'articles_count'=>$this->when(isset($this->articles_count),function(){
              return $this->articles_count;
          }),
          'created_at'=>$this->created_at,
        ];
    }
}
