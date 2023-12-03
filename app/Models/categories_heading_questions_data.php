<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class categories_heading_questions_data extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['category_heading_question_id','ar_name','en_name','type'];

    public function heading(){
        return $this->belongsTo(categories_heading_questions::class,'category_heading_question_id');
    }

    public function image(){
        return $this->morphOne(images::class,'imageable');
    }

    public function selections(){
        return $this->morphMany(select_options::class,'selectionable');
    }
}
