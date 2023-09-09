<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class articles extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title','description'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function seen(){
        return $this->hasOne(seen::class,'item_id');
    }

    public function images(){
        return $this->morphMany(images::class,'imageable');
    }

    public function comments(){
        return $this->hasMany(articles_comments::class,'article_id');
    }
}
