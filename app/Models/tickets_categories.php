<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tickets_categories extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function categories(){
        return $this->hasMany(tickets::class,'ticket_cat_id');
    }
}
