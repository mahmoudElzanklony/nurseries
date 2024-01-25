<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class multi_selections extends Model
{
    use HasFactory;

    protected $fillable = ['selectable_id','selectionable_type','ar_name','en_name'];

    public function selectionable(){
        return $this->morphTo();
    }
}
