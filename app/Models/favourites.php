<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favourites extends Model
{
    use HasFactory;

    protected $fillable =  ['user_id','item_id','type'];

    public function product(){
        return $this->belongsTo(products::class,'item_id');
    }

    public function article(){
        return $this->belongsTo(articles::class,'article_id');
    }
}
