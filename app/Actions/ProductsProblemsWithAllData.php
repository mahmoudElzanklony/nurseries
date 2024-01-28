<?php


namespace App\Actions;


use App\Models\products_problems;

class ProductsProblemsWithAllData
{
    public static function get(){
        return  products_problems::query()->with('product')
            ->when(auth()->user()->role->name == 'client' || auth()->user()->role->name == 'company',function($e){
                $e->where('user_id','=',auth()->id());
            })->with('images')->orderBy('id','DESC');
    }

    public static function test($data){
        return  $data;
    }
}
