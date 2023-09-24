<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AllSellersDataController extends Controller
{
    //
    public function index(){
        $users = User::query()->whereHas('role',function($e){
            $e->where('name','=','seller');
        })->orderBy('id','DESC')->paginate(10);
        return UserResource::collection($users);

    }
}
