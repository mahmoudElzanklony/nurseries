<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class articles_comments extends Model
{
    use HasFactory;

    protected $fillable = ['article_id','user_id','comment'];
}
