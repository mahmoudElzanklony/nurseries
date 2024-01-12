<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ads extends Model
{
    use HasFactory;
    protected $fillable = ['ar_name','en_name','order'];

    public function image()
    {
        return $this->morphOne(images::class,'imageable');
    }
}
