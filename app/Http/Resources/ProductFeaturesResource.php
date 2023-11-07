<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductFeaturesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        try{
            $feature = ProductFeatureResource::make($this->whenLoaded('feature'));
        }catch (\Throwable $e){
            $feature = null;
        }
        return [
          'id'=>$this->id,
          'category_feature_id'=>$this->category_feature_id,
          'price'=>$this->price,
          'note'=>$this->note,
          'created_at'=>$this->created_at,
          'feature'=>$this->when(isset($feature),function () use ($feature){
              return $feature;
          }),

        ];
    }
}
