<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerResource;
use App\Models\followers;
use App\Models\User;
use App\Services\users\toggle_data;
use Illuminate\Http\Request;

class FollowersController extends Controller
{
    //
    public function toggle(){
        $user = User::query()->findOrFail(request('user_id'));
        return toggle_data::toggle_following($user->id);
    }

    public function all(){
        $data = followers::query()->where('user_id',auth()->id())->with('follower')
            ->orderBy('id','DESC')->paginate(15);
        return FollowerResource::collection($data);
    }
}
