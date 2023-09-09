<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders_items_rates extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','order_item_id','comment','rate'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
