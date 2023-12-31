<?php


namespace App\Services\users;


use App\Http\traits\messages;
use App\Models\articles_permission;
use App\Models\favourites;
use App\Models\followers;
use App\Models\likes;
use Illuminate\Support\Facades\DB;

class toggle_data
{
    public static function toggle_fav($product_id, $type = 'product'){
        $fav = favourites::query()->where('user_id','=',auth()->id())
            ->where('type','=',$type)
            ->where('item_id','=',$product_id)->first();
        if($fav != null){
            $fav->delete();
            $msg = trans('messages.removed_from_fav_successfully');
            $status = false;
        }else{
            favourites::query()->create([
               'user_id'=>auth()->id(),
               'item_id'=>$product_id,
               'type'=>$type
            ]);
            $status = true;
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
            $status = false;
        }else{
            likes::query()->create([
                'user_id'=>auth()->id(),
                'item_id'=>$id,
                'type'=>$table,
            ]);
            $msg = trans('messages.added_like_successfully');
            $status = true;
        }
        return messages::success_output($msg,['status'=>$status]);
    }

    public static function toggle_following($user_id){
        $follow_obj = followers::query()->where('user_id','=',auth()->id())
            ->where('following_id','=',$user_id)->first();
        if($follow_obj != null){
            $follow_obj->delete();
            $msg = trans('messages.removed_from_following_successfully');
            $status = false;
        }else{
            followers::query()->create([
                'user_id'=>auth()->id(),
                'following_id'=>$user_id
            ]);
            $msg = trans('messages.added_to_following_successfully');
            $status = true;
        }
        return messages::success_output($msg,['status'=>$status]);
    }

    public static function toggle_article_permission($user_id){
        $obj  = articles_permission::query()->where('user_id','=',$user_id)
            ->first();
        if($obj != null){
            $obj->delete();
            $msg = trans('messages.seller_remove_permission_to_add_articles');
            $status = false;
        }else{
            articles_permission::query()->create([
                'user_id'=>$user_id,
            ]);
            $msg = trans('messages.seller_has_permission_to_add_articles');
            $status = true;
        }
        return messages::success_output($msg,['status'=>$status]);
    }

}
