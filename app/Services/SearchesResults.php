<?php


namespace App\Services;


use App\Models\searches;

class SearchesResults
{
    public static function last_searches($type){
        $data = searches::query()
            ->where('user_id','=',auth()->id())
            ->where('type','=',$type)->orderBy('id','DESC')->paginate(15);
        return $data;
    }
    public static function added_to_search($item_id,$type){
        $output = searches::query()->where([
            'user_id'=>auth()->id(),
            'item_id'=>$item_id,
            'type'=>$type,
        ])->first();
        if($output != null){
            $output->delete();
        }
        searches::query()->create([
            'user_id'=>auth()->id(),
            'item_id'=>$item_id,
            'type'=>$type,
        ]);

    }
}
