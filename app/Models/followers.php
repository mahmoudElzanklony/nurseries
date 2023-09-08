<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class followers extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','following_id'];

    public function follower(){
        return $this->belongsTo(User::class,'following_id');
    }
}
