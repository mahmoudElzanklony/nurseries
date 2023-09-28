<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tickets extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_cat_id','user_id','title','message'];

    public function ticket_cat(){
        return $this->belongsTo(tickets_categories::class,'ticket_cat_id');
    }
}
