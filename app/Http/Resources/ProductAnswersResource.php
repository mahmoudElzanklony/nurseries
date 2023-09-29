<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAnswersResource extends JsonResource
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
          'category_heading_questions_data_id'=>$this->category_heading_questions_data_id,
          'answer'=>$this->{app()->getLocale().'_answer'},
          'image'=>ImagesResource::make($this->question->image),
          'created_at'=>$this->created_at,
         // 'question'=>ProductQuestionResource::make($this->whenLoaded('question')),

        ];
    }
}
