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
        $output = searches::query()->where([
            'user_id'=>auth()->id(),
            'item_id'=>$item_id,
            'type'=>$type,
        ])->first();
        if($output != null){
            $output->updated_at = date('Y-m-d H:i:s');
            $output->save();
        }else{
            searches::query()->create([
                'user_id'=>auth()->id(),
                'item_id'=>$item_id,
                'type'=>$type,
            ]);
        }

    }
}
