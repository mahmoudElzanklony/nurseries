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
            ->with(['user','images','comments'=>function($e){
                $e->with('user');
            },'seen'])->withCount('likes')
            ->orderBy('id','DESC');
    }
}
