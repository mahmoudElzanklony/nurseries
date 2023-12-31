<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class governments extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = ['ar_name','en_name','country_id'];

    public function country(){
        return $this->belongsTo(countries::class,'country_id');
    }
}
