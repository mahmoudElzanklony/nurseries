<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
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
          'products'=>$this->when(isset($this->products_count),function (){
              return $this->products_count;
          }),
          'image'=>ImagesResource::make($this->whenLoaded('image')),
          'features'=>CategoryFeaturesResource::collection($this->whenLoaded('features')),
          'heading_questions'=>CategoryHeadingQuestionsResource::collection($this->whenLoaded('heading_questions')),
          'created_at'=>$this->created_at
        ];
    }
}
