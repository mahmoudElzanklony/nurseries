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
                },'images','rates.user','wholesale_prices','discounts'
                ,'features'=>function($f){
                    $f->with('feature');
                },'answers'=>function($e){
                    $e->with('question');
                }]);
        return $data;
    }
}
