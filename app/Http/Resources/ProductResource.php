<?php

namespace App\Http\Resources;

use App\Actions\CheckPlaceMapLocation;
use App\Actions\CheckProductSupportDeliveryToUserAddress;
use App\Actions\DefaultAddress;
use App\Actions\DeliveryOfOrder;
use App\Actions\SellerRateAVG;
use App\Actions\WantToBeRated;
use App\Models\followers;
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
        $default_address = DefaultAddress::get();
        $delivery = CheckPlaceMapLocation::check_delivery($this->id,$default_address);
        if(sizeof($this->rates) > 0){
            $rate_bars = [
              0,0,0,0,0
            ];
            foreach($this->rates as $rate){
                $rate_bars[$rate->rate_product_info - 1]++;
            }
        }
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'name'=>$this->{app()->getLocale().'_name'},
            'description'=>$this->{app()->getLocale().'_description'},
            'quantity'=>$this->quantity,
            'main_price'=>$this->main_price,
            'created_at'=>$this->created_at,
            'category'=>CategoriesResource::make($this->whenLoaded('category')),
            'images'=>ImagesResource::collection($this->whenLoaded('images')),
            'image'=>ImagesResource::make($this->whenLoaded('image')),
            'favourite'=>$this->favourite != null ? true:false,
            'seen'=>$this->seen->count ?? 0,
            'likes_count'=>$this->likes_count,
            'last_four_likes'=>UserResource::collection($this->whenLoaded('last_four_likes')),
            'want_rate'=>WantToBeRated::check($this->id),
            'rates'=>RateResource::collection($this->whenLoaded('rates')),
            'rates_bar'=>$rate_bars ?? [],
            'avg_rates_product'=>round($this->rates->avg('rate_product_info'),2),
            'avg_rates_seller'=>round(($seller_avg_rate['avg_services']+$seller_avg_rate['avg_delivery'])/2,2),
            'is_following'=>auth()->check() && followers::query()->where('user_id',auth()->id())->where('following_id',$this->user_id)->first() != null ? 1:0,
            'user'=>UserResource::make($this->whenLoaded('user')),
            'cares'=>ProductCareResource::collection($this->whenLoaded('cares')),
            'features'=>ProductFeaturesResource::collection($this->whenLoaded('features')),
            'answers'=>ProductAnswersResource::collection($this->whenLoaded('answers')),
            'delivery'=>auth()->check() && $delivery != false ? $delivery :trans('errors.product_doesnt_support_delivery'),
            'discounts'=>ProductDiscountsResource::collection($this->whenLoaded('discounts')),
            'wholesale_prices'=>ProductWholesalePricesResource::collection($this->whenLoaded('wholesale_prices')),
        ];
    }
}
