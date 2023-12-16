<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductQuestionResource extends JsonResource
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
          'ar_name'=>$this->ar_name,
          'en_name'=>$this->en_name,
          'name'=>$this->{app()->getLocale().'_name'},
          'type'=>$this->when(isset($this->type),function (){
             return $this->type;
          }),
            'heading'=>CategoryHeadingQuestionsResource::make($this->whenLoaded('heading')),
            //  'image'=>ProductQuestionResource::make($this->whenLoaded('image')),
          'options'=>SelectOptionsResource::collection($this->whenLoaded('options')),
          'created_at'=>$this->created_at,

        ];
    }
}
