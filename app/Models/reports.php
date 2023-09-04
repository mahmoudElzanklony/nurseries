<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reports extends Model
{
    use HasFactory;
    // type ==> wallet(withdraw)
    // type ==> wallet(add)
    // type ==> package(buy)
    // type ==> package(accept)
    // type ==> package(marketer_withdraw_pending)
    // type ==> package(marketer_withdraw_success)
    // type ==> tickets(client) == mean client reply
    // type ==> tickets(admin) == mean  admin reply
    // type ==> tickets(admin) == mean  admin reply
    // type ==> etc.......
    protected $fillable = ['user_id','info','type'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
