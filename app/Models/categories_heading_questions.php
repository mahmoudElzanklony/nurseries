<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class categories_heading_questions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['category_id','name'];

    public function questions_data(){
        return $this->hasMany(categories_heading_questions_data::class,'category_heading_question_id');
    }
}
