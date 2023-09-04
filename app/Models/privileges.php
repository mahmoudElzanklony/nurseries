<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class privileges extends Model
{
    use HasFactory;

    protected $fillable = ['role_id','page_id','add','update','delete'];

    public function role(){
        return $this->belongsTo(roles::class,'role_id');
    }

    public function page(){
        return $this->belongsTo(pages::class,'page_id');
    }
}
