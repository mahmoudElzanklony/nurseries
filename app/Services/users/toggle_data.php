<?php


namespace App\Services\users;


use App\Http\traits\messages;
use App\Models\favourites;
use App\Models\followers;
use App\Models\likes;
use Illuminate\Support\Facades\DB;

class toggle_data
{
    public static function toggle_fav($product_id){
        $fav = favourites::query()->where('user_id','=',auth()->id())
            ->where('product_id','=',$product_id)->first();
        if($fav != null){
            $fav->delete();
            $msg = trans('messages.removed_from_fav_successfully');
            $status = 0;
        }else{
            favourites::query()->create([
               'user_id'=>auth()->id(),
               'product_id'=>$product_id
            ]);
            $status = 1;
            $msg = trans('messages.added_to_fav_successfully');
        }
        return messages::success_output($msg,['status'=>$status]);
    }


    public static function toggle_like($id,$table){
        $like = likes::query()
            ->where('user_id','=',auth()->id())
            ->where('item_id','=',$id)
            ->where('type','=',$table)->first();
        if($like != null){
            $like->delete();
            $msg = trans('messages.removed_like_successfully');
            $status = 0;
        }else{
            likes::query()->create([
                'user_id'=>auth()->id(),
                'item_id'=>$id,
                'type'=>$table,
            ]);
            $msg = trans('messages.added_like_successfully');
            $status = 1;
        }
        return messages::success_output($msg,['status'=>$status]);
    }

    public static function toggle_following($user_id){
        $follow_obj = followers::query()->where('user_id','=',auth()->id())
            ->where('following_id','=',$user_id)->first();
        if($follow_obj != null){
            $follow_obj->delete();
            $msg = trans('messages.removed_from_following_successfully');
            $status = 0;
        }else{
            followers::query()->create([
                'user_id'=>auth()->id(),
                'following_id'=>$user_id
            ]);
            $msg = trans('messages.added_to_following_successfully');
            $status = 1;
        }
        return messages::success_output($msg,['status'=>$status]);
    }

}
