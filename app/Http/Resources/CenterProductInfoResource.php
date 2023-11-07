<?php

namespace App\Http\Resources;

use App\Actions\ProductStatisticsForSeller;
use App\Actions\WantToBeRated;
use App\Models\followers;
use App\Models\users_products_cares;
use Illuminate\Http\Resources\Json\JsonResource;

class CenterProductInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $features = collect($this->features)->map(function ($item) {
            return (object) $item;
        })->toArray();
        $answers = collect($this->answers)->map(function ($item) {
            return (object) $item;
        })->toArray();
        $discounts = collect($this->discounts)->map(function ($item) {
            return (object) $item;
        })->toArray();
        return [
            'id'=>$this->id,
            'name'=>$this->{app()->getLocale().'_name'},
            'description'=>$this->{app()->getLocale().'_description'},
            'quantity'=>$this->quantity,
            'main_price'=>$this->main_price,
            'created_at'=>$this->created_at,
            'images'=>ImagesResource::collection($this->images),


            'features'=>ProductFeaturesResource::collection($features),
            'answers'=>ProductAnswersResource::collection($answers),
            'discounts'=>ProductDiscountsResource::collection($discounts),
        ];
    }
}
