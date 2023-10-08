<?php


namespace App\Actions;


use App\Models\products;

class ProductWithAllData
{
    public static function get(){
        $authentication = GetAuthenticatedUser::get_info();
        $data = products::query()
            ->when($authentication != null , function ($e){
                $e->with('favourite');
            })
            ->withCount('likes')
            ->with(['category','user','seen','last_four_likes',
                'cares'=>function($e) use ($authentication){
                    $e->with('care')->when($authentication != null , function($q){
                        $q->with('next_time',function($q){
                           $q->where('user_id','=',auth()->id());
                        });
                    });
                },'images','rates.user','wholesale_prices','discounts'=>function($e){
                    $e->whereRaw('CURDATE() >= start_date and CURDATE() <= end_date');
                }
                ,'features.feature.image','answers'=>function($e){
                    $e->with('question');
                }]);
        return $data;
    }
}
