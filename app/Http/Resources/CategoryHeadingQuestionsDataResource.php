<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryHeadingQuestionsDataResource extends JsonResource
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
            'type'=>$this->type,
            'selections'=>$this->when($this->type == 'selection' || $this->type == 'select',function (){
                return SelectionResource::collection($this->selections);
            }),
            'image'=>ImagesResource::make($this->whenLoaded('image')),
        ];
    }
}
