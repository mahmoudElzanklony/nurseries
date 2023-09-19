<?php

namespace App\Http\Resources;

use App\Actions\SellerRateAVG;
use App\Actions\WantToBeRated;
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
        $seller_avg_rate = SellerRateAVG::get($this->user_id);
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
            'want_rate'=>WantToBeRated::check($this->id),
            'rates'=>RateResource::collection($this->whenLoaded('rates')),
            'avg_rates_product'=>round($this->rates->avg('rate_product_info'),2),
            'avg_rates_seller'=>round(($seller_avg_rate['avg_services']+$seller_avg_rate['avg_delivery'])/2,2),
            'user'=>UserResource::make($this->whenLoaded('user')),
            'cares'=>ProductCareResource::collection($this->whenLoaded('cares')),
            'features'=>ProductFeaturesResource::collection($this->whenLoaded('features')),
            'answers'=>ProductAnswersResource::collection($this->whenLoaded('answers')),
            'discounts'=>ProductDiscountsResource::collection($this->whenLoaded('discounts')),
            'wholesale_prices'=>ProductWholesalePricesResource::collection($this->whenLoaded('wholesale_prices')),
        ];
    }
}
