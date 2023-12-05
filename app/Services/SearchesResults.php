<?php


namespace App\Services;


use App\Models\searches;

class SearchesResults
{
    public static function last_searches($type){
        $data = searches::query()
            ->where('user_id','=',auth()->id())
            ->where('type','=',$type)->orderBy('updated_at','DESC')->paginate(15);
        return $data;
    }
    public static function added_to_search($item_id,$type){
        searches::query()->updateOrCreate([
            'user_id'=>auth()->id(),
            'item_id'=>$item_id,
            'type'=>$type,
        ],[
            'user_id'=>auth()->id(),
            'item_id'=>$item_id,
            'type'=>$type,
        ]);
    }
}
