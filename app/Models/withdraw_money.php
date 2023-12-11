<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class withdraw_money extends Model
{
    use HasFactory;

    protected $fillable = ['item_id','item_type','price','message','status'];
}
