<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id','receiver_id','ar_content','en_content','url','seen'];


    public function sender(){
        return $this->belongsTo(User::class,'sender_id')->withTrashed();
    }

    public function receiver(){
        return $this->belongsTo(User::class,'receiver_id')->withTrashed();
    }
}
