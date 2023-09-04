<?php

namespace App\Http\Controllers\Api\auth;

use App\Http\Controllers\classes\auth\AuthServicesClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\usersFormRequest;
use App\Http\traits\messages;
use App\Models\cities;
use App\Models\countries;
use App\Models\roles;
use App\Models\tenants;
use App\Services\auth\register_service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerApi extends AuthServicesClass
{
    use messages;

    public function test(){
        // for testing
        $perPage = 1; // Number of results per page
        $allUsers = new Collection(); // Create an empty collection to hold all the results

        for ($page = 1; ; $page++) {
            $users = tenants::query()->paginate($perPage, ['*'], 'page', $page); // Get the current page of results

            if ($users->isEmpty()) {
                break; // Exit the loop if there are no more results
            }
            $allUsers = $allUsers->merge($users->items()); // Add the current page of results to the collection
        }

        // Do something with the collection of all results
        return $allUsers;
    }

    public function validate_user(){
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json($user);
    }

    public function login_api(){
        $data = Validator::make(request()->all(),[
            'email'=>'required',
            'password'=>'required',
        ]);

        if(sizeof($data->errors()) == 0) {

            $credential = request()->only(['email', 'password']);
            $token = auth('api')->attempt($credential);
            if(!$token){
                return messages::error_output(trans('errors.unauthenticated'));
            }else {
                $user = auth('api')->user();
                $role = roles::query()->find($user->role_id);
                $user['role'] = $role;
                $user['token'] =  $token;
                $user['country_id'] =  cities::query()->find($user['city_id'])->country_id;
                return messages::success_output('',$user);
            }
        }else{
            return messages::error_output($data->errors());
        }
    }

    public function logout_api(){
        session()->forget('type');
        auth()->logout();
        JWTAuth::getToken(); // Ensures token is already loaded.
        JWTAuth::invalidate(true);
        return messages::success_output('logout successfully');

    }

    public function user(){
        return auth()->user();
    }

}
