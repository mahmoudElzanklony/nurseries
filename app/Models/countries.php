<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class countries extends Model
{
    use HasFactory;

    protected $fillable = ['ar_name','en_name','code'];


    public function goverments(){
        return  $this->hasMany(governments::class,'country_id');
    }

    public function image(){
        return $this->morphOne(images::class,'imageable');
    }

    public function users(){
        return $this->hasMany(User::class,'country_id');
    }


}
