<?php


namespace App\Actions;


use App\Models\articles;

class ArticlesWithAllData
{
    public static function get(){
        return articles::query()
            ->with(['user','images','comments','seen'])
            ->orderBy('id','DESC');
    }
}
