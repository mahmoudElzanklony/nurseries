<?php

namespace App\Http\Resources;

use App\Actions\SellerRateAVG;
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
          'role'=>$this->whenLoaded('role'),
          'token'=>isset($this->token) ? $this->token : null,
          'avg_rates'=>isset($seller_avg_rate)  ?
                       round(($seller_avg_rate['avg_services']+$seller_avg_rate['avg_delivery'])/2,2) : null,
          'image'=>$this->image != null ? ImagesResource::make($this->image) : ['name'=>'users/default.png'],
          'created_at'=>$this->created_at,
        ];
    }
}
