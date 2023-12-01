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
        $arr =  [
          'id'=>$this->id,
          'category_heading_questions_data_id'=>$this->category_heading_questions_data_id,
          'answer'=>$this->{app()->getLocale().'_answer'},
          'created_at'=>$this->created_at,

         /* 'question'=>$this->when(method_exists($this,'whenLoaded') && method_exists($this,'relationLoaded'),function (){
              return ProductQuestionResource::make($this->whenLoaded('question'));
          }),*/
          'question'=>ProductQuestionResource::make($this->whenLoaded('question')),

        ];
        try{
            $arr['image'] = ImagesResource::make($this->question->image);
        }catch (\Throwable $e){

        }
        return $arr;
    }
}
