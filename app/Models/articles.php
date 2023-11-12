<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class articles extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','title','category_id','description'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function category(){
        return $this->belongsTo(categories::class,'category_id');
    }

    public function likes(){
        return $this->hasMany(likes::class,'item_id');
    }

    public function like(){
        return $this->hasOne(likes::class,'item_id')
            ->where('type','=','article')
            ->where('user_id',auth()->id());
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

    public function favourite(){
        return $this->hasOne(favourites::class,'item_id')
            ->where('type','=','article')
            ->where('user_id',auth()->id());
    }
}
