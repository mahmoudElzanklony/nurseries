<?php


namespace App\Actions;


use App\Models\articles;

class ArticlesWithAllData
{
    public static function get(){
        return articles::query()
            ->when(GetAuthenticatedUser::get_info() != null , function ($e){
                $e->with('like');
            })
            ->when(GetAuthenticatedUser::get_info() != null && auth()->user()->role->name == 'seller' , function ($e){
                $e->where('user_id','=',auth()->id());
            })
            ->with(['user','category','images','comments'=>function($e){
                $e->with('user');
            },'seen'])->withCount('likes')
            ->orderBy('id','DESC');
    }
}
