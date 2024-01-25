<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ai_questions extends Model
{
    use HasFactory;

    protected $fillable = ['ar_name','en_name','type'];

    public function options(){
        return $this->morphMany(multi_selections::class,'selectable');
    }
}
