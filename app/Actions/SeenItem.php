<?php


namespace App\Actions;


use App\Models\seen;

class SeenItem
{
    public static function add($item_id,$type){
        $item  = seen::query()->firstOrCreate([
            'item_id'=>$item_id,
            'type'=>$type,
        ],[
            'item_id'=>$item_id,
            'type'=>$type,
            'count'=>0
        ]);
        $item->count = $item->count + 1;
        $item->save();
    }
}
