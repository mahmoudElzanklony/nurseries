<?php


namespace App\Actions;


use App\Models\products_problems;

class ProductsProblemsWithAllData
{
    public static function get(){
        return  products_problems::query()
            ->when(auth()->user()->role->name == 'client' || auth()->user()->role->name == 'company',function($e){
                $e->where('user_id','=',auth()->id());
            })->with('images')->orderBy('id','DESC');
    }
}
