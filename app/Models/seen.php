<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class seen extends Model
{
    use HasFactory;

    protected $fillable = ['item_id','type','count'];

}
