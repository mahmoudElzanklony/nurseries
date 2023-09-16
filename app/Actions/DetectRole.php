<?php


namespace App\Actions;


use App\Models\User;

class DetectRole
{
    public static function get(){
        $user = User::query()->with('role')->find(auth()->id());
        return $user->role->name;
    }
}
