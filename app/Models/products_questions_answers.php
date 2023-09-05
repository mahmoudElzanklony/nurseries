<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products_questions_answers extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','category_heading_questions_data_id','ar_answer','en_answer'];

    public function question(){
        return $this->belongsTo(categories_heading_questions_data::class,'category_heading_questions_data_id');
    }
}
