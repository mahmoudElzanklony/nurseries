<?php

namespace App\Http\Resources;

use App\Actions\CheckPlaceMapLocation;
use App\Actions\CheckProductSupportDeliveryToUserAddress;
use App\Actions\DefaultAddress;
use App\Actions\DeliveryOfOrder;
use App\Actions\ProductStatisticsForSeller;
use App\Actions\SellerRateAVG;
use App\Actions\WantToBeRated;
use App\Models\followers;
use App\Models\orders_items;
use App\Models\users_products_cares;
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

        if(str_contains(request()->fullUrl(), 'orders') == false){
            $seller_avg_rate = SellerRateAVG::get($this->user_id);
            $default_address = DefaultAddress::get();
            $delivery = CheckPlaceMapLocation::check_delivery($this->id,$default_address);
            try {
                if (sizeof($this->rates) > 0) {
                    $rate_bars = [
                        0, 0, 0, 0, 0
                    ];
                    foreach ($this->rates as $rate) {
                        $rate_bars[$rate->rate_product_info - 1]++;
                    }
                }
            }catch (\Throwable $exception){
                $rate_bars = [];
            }
        }else{
            $rate_bars = [];
            $seller_avg_rate = [];
        }

        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'name'=>$this->{app()->getLocale().'_name'},
            'description'=>$this->{app()->getLocale().'_description'},
            'plant_type'=>[
                'ar_name'=>$this->plant_type == 'inner' ? 'داخلي':'خارجي',
                'en_name'=>$this->plant_type,
            ],
            'quantity'=>$this->quantity,
            'main_price'=>$this->main_price,
            'status'=>$this->status,
            'created_at'=>$this->created_at,
            'category'=>CategoriesResource::make($this->whenLoaded('category')),
            'images'=>ImagesResource::collection($this->whenLoaded('images')),
            'image'=>ImagesResource::make($this->whenLoaded('image')),
            'favourite'=>$this->favourite != null ? true:false,
            'seen'=>$this->seen->count ?? 0,
            'likes_count'=>$this->likes_count,
            'last_four_likes'=>UserResource::collection($this->whenLoaded('last_four_likes')),
            'problems'=>ProductProblemResource::collection($this->whenLoaded('problems')),
            'want_rate'=>WantToBeRated::check($this->id),
            'rates'=>RateResource::collection($this->whenLoaded('rates')),
            'good_rates_percentage'=>$this->when(str_contains(request()->fullUrl(), 'orders') == false,function (){
               if(sizeof($this->rates) > 0){
                   return collect($this->rates)->filter(function ($e){
                       return $e->rate_product_info >= 4;
                   })->map(function ($e){
                       return $e->rate_product_info;
                   })->count() / sizeof($this->rates) * 100;
               }else{
                   return 0;
               }
            }),
            'product_as_description'=>$this->when(str_contains(request()->fullUrl(), 'orders') == false,function (){
                if(sizeof($this->rates) > 0){
                    return collect($this->rates)->filter(function ($e){
                            return $e->rate_product_info;
                        })->map(function ($e){
                            return $e->rate_product_info;
                        })->sum() / sizeof($this->rates) / 5 * 100;
                }else{
                    return 0;
                }
            }),
            'rates_bar'=>$rate_bars ?? [],
            'avg_rates_product'=>$this->when(str_contains(request()->fullUrl(), 'orders') == false,function (){
                return round($this->rates->avg('rate_product_info'),2);
            }),
            'avg_rates_seller'=>$this->when(str_contains(request()->fullUrl(), 'orders') == false,function() use ($seller_avg_rate){

               return round(($seller_avg_rate['avg_services']+$seller_avg_rate['avg_delivery'])/2,2);
            }),
            'is_following'=>auth()->check() && followers::query()->where('user_id',auth()->id())->where('following_id',$this->user_id)->first() != null ? true:false,
            'user'=>UserResource::make($this->whenLoaded('user')),
            'statistics'=>$this->when(auth()->check() && (auth()->user()->role->name == 'seller' || auth()->user()->role->name == 'admin'),function (){
                return ProductStatisticsForSeller::get($this->id);
            }),
            'sales_info'=>$this->when(auth()->check() && (auth()->user()->role->name != 'seller'),function (){
                return ProductStatisticsForSeller::get_for_company($this->id);
            }),
            'changeable_prices'=>ProductPriceChangeResource::collection($this->whenLoaded('changeable_prices')),
            'cares'=>ProductCareResource::collection($this->whenLoaded('cares')),
            'has_care'=>$this->when(auth()->check() && auth()->user()->role->name == 'client',function (){
                $check = users_products_cares::query()
                    ->where('user_id','=',auth()->id())
                    ->where('product_id','=',$this->id)->first();
                if($check != null){
                    return true;
                }else{
                    return false;
                }
            }),
            'order_check_created_at'=>$this->when(isset($this->last_order_item) &&
                $this->last_order_item != null &&
                $this->last_order_item->order != null
                ,function (){
                    return $this->last_order_item->order->created_at;
            }),
            'shipments'=>$this->when(isset($this->last_order_item) &&
                                        $this->last_order_item != null &&
                                        $this->last_order_item->order != null
                                       ,function (){
                return OrderShipmentsInfo::collection($this->last_order_item->order->shipments_info);
            }),

            'features'=>ProductFeaturesResource::collection($this->whenLoaded('features')),
            'deliveries'=>ProductDeliveriesResource::collection($this->whenLoaded('deliveries')),
            'answers'=>ProductAnswersResource::collection($this->whenLoaded('answers')),
            'delivery'=>auth()->check() && $delivery != false ? $delivery :null,
            'delivery_ar'=>auth()->check() && $delivery == false ? trans('errors.product_doesnt_support_delivery'):null,
            'discounts'=>ProductDiscountsResource::collection($this->whenLoaded('discounts')),
            'wholesale_prices'=>ProductWholesalePricesResource::collection($this->whenLoaded('wholesale_prices')),

        ];
    }
}
