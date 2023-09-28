<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tickets_messages extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id','user_id','status','message'];

    public function ticket(){
        return $this->belongsTo(tickets::class,'ticket_id');
    }
}
