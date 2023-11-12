<?php

namespace App\Http\Resources;

use App\Actions\GetAuthenticatedUser;
use App\Models\favourites;
use Illuminate\Http\Resources\Json\JsonResource;
use function OpenAI\ValueObjects\Transporter\data;

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
          'category'=>CategoryResource::make($this->whenLoaded('category')),
          'seen'=>$this->seen->count ?? 0,
          'likes_count'=>$this->likes_count,
          'like'=>$this->like != null ? true:false,
          'images'=>ImagesResource::collection($this->whenLoaded('images')),
          'favourite'=>$this->when(true,function (){
              $authentication = GetAuthenticatedUser::get_info();
              if($authentication != null){
                  $check = favourites::query()
                      ->where('user_id','=',$authentication->id)
                      ->where('item_id','=',$this->id)
                      ->where('type','=','article')->first();
                  if($check != null){
                      return true;
                  }else {
                      return false;
                  }
              }else{
                  return false;
              }
          }),
          'comments'=>CommentResource::collection($this->whenLoaded('comments')),
          'created_at'=>$this->created_at

        ];
    }
}
