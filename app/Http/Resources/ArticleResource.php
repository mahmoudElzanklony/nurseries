<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
          'title'=>$this->title,
          'description'=>$this->description,
          'user'=>UserResource::make($this->whenLoaded('user')),
          'seen'=>$this->seen->count ?? 0,
          'images'=>ImagesResource::collection($this->whenLoaded('images')),
          'comments'=>CommentResource::collection($this->whenLoaded('comments')),
          'created_at'=>$this->created_at->format('Y m d, h:i A'),

        ];
    }
}
