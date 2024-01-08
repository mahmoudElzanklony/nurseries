<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class categories extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['ar_name','en_name'];

    public function features(){
        return $this->hasMany(categories_features::class,'category_id');
    }

    public function heading_questions(){
        return $this->hasMany(categories_heading_questions::class,'category_id');
    }

    public function questions()
    {
        return $this->hasManyThrough(categories_heading_questions_data::class,categories_heading_questions::class,'category_id','category_heading_question_id');
    }
}
