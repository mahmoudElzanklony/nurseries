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
            'name'=>$this->{app()->getLocale().'_name'},
            'description'=>$this->{app()->getLocale().'_description'},
            'quantity'=>$this->quantity,
            'main_price'=>$this->main_price,
            'created_at'=>$this->created_at->format('Y m d, h:i A'),
            'images'=>ImagesResource::collection($this->whenLoaded('images')),
            'favourite'=>FavouriteResource::make($this->favourite),
            'features'=>ProductFeaturesResource::collection($this->whenLoaded('features')),
            'answers'=>ProductAnswersResource::collection($this->whenLoaded('answers')),
            'discounts'=>ProductDiscountsResource::collection($this->whenLoaded('discounts')),
            'wholesale_prices'=>ProductWholesalePricesResource::collection($this->whenLoaded('wholesale_prices')),
        ];
    }
}
