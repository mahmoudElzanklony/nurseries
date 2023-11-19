<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class articles_permission extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];
}
