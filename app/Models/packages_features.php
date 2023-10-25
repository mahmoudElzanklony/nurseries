<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class packages_features extends Model
{
    use HasFactory;

    protected $fillable = ['package_id','name'];
}
