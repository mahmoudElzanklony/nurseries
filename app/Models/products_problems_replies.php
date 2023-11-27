<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products_problems_replies extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','product_problem_id','reply'];
}
