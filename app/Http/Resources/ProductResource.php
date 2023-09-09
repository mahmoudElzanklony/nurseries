<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'name'=>$this->{app()->getLocale().'_name'},
            'description'=>$this->{app()->getLocale().'_description'},
            'quantity'=>$this->quantity,
            'main_price'=>$this->main_price,
            'created_at'=>$this->created_at->format('Y m d, h:i A'),
            'images'=>ImagesResource::collection($this->whenLoaded('images')),
            'favourite'=>FavouriteResource::make($this->favourite),
            'seen'=>$this->seen->count ?? 0,
            'likes_count'=>$this->likes_count,
            'rates'=>RateResource::collection($this->whenLoaded('rates')),
            'avg_rates'=>round($this->rates->avg('rate'),2),
            'user'=>UserResource::make($this->whenLoaded('user')),
            'features'=>ProductFeaturesResource::collection($this->whenLoaded('features')),
            'answers'=>ProductAnswersResource::collection($this->whenLoaded('answers')),
            'discounts'=>ProductDiscountsResource::collection($this->whenLoaded('discounts')),
            'wholesale_prices'=>ProductWholesalePricesResource::collection($this->whenLoaded('wholesale_prices')),
        ];
    }
}
