<?php


namespace App\Actions;


use App\Models\products;

class ProductWithAllData
{
    public static function get(){
        $data = products::query()
            ->when(GetAuthenticatedUser::get_info() != null , function ($e){
                $e->with('favourite');
            })
            ->withCount('likes')
            ->with(['category','user','seen',
                'cares'=>function($e){
                    $e->with('care');
                },'images','rates.user','wholesale_prices','discounts'=>function($e){
                    $e->whereRaw('CURDATE() >= start_date and CURDATE() <= end_date');
                }
                ,'features'=>function($f){
                    $f->with('feature');
                },'answers'=>function($e){
                    $e->with('question');
                }]);
        return $data;
    }
}
