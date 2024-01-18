<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\ImageRenderer;

class CategoryFeaturesResource extends JsonResource
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
            'image'=>ImagesResource::make($this->image),
        ];
    }
}
