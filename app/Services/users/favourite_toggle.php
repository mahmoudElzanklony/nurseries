<?php


namespace App\Services\users;


use App\Http\traits\messages;
use App\Models\favourites;

class favourite_toggle
{
    public static function toggle($listing_id){
        $fav = favourites::query()->where('user_id','=',auth()->id())
            ->where('listing_id','=',$listing_id)->first();
        if($fav != null){
            $fav->delete();
            $msg = trans('messages.removed_from_fav_successfully');
        }else{
            favourites::query()->create([
               'user_id'=>auth()->id(),
               'listing_id'=>$listing_id
            ]);
            $msg = trans('messages.added_to_fav_successfully');
        }
        return messages::success_output([$msg]);
    }
}
