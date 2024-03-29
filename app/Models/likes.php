<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class likes extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','item_id','type'];

    public function user(){
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }
}
