<?php

namespace App\Http\Controllers\Api\auth;

use App\Actions\DefaultAddress;
use App\Http\Controllers\classes\auth\AuthServicesClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\usersFormRequest;
use App\Http\Resources\UserResource;
use App\Http\traits\messages;
use App\Models\cities;
use App\Models\countries;
use App\Models\orders;
use App\Models\roles;
use App\Models\tenants;
use App\Models\User;
use App\Models\user_devices;
use App\Services\auth\register_service;
use App\Services\SendSmsExternalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerApi extends AuthServicesClass
{
    use messages;
    private $externalSmsService;
    public function __construct(SendSmsExternalService $externalService)
    {
        $this->externalSmsService = $externalService;
    }
    public function test(){
       // echo dirname('routes/api.php');
        $output =  orders::query()
            ->with('last_shipment_info')
            ->whereHas('last_shipment_info',function($query){
                $query->orderByDesc('id')->where('id',request('status'));
            })->find(10);
        return $output;
       // echo date('Y/m/d H:m:s');
    }

    public function test_sms()
    {

        return $this->externalSmsService->send('966500462405','تيست تيست');
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
    public function get_user_by_token(){
        if(request()->hasHeader('token')) {
            $token = request()->header('token');
            request()->headers->set('token', (string)$token, true);
            request()->headers->set('Authorization', 'Bearer ' . $token, true);
            try {
                $token = JWTAuth::parseToken();
                $user = $token->authenticate();

                if ($user == false) {
                    return messages::error_output(['invalid credential']);
                }
                $user['role'] = roles::query()->find($user->role_id);
                if($user->email == '' && $user->username == '') {
                    $user['new_user'] = true;
                }else{
                    $user['new_user'] = false;
                }
                // token
                $user['token'] =  request()->header('token');
                $user['default_address'] = DefaultAddress::get($user->id);

                return messages::success_output('',UserResource::make($user),[
                    'new_user'=>$user['new_user']
                ]);



            } catch (\Exception $e) {
                return messages::error_output([$e->getMessage()]);
            }
        }
    }

    public function login_api(){
        $data = Validator::make(request()->all(),[
            'phone'=>'required',
            'activation_code'=>'required',
            'app'=>'required',
        ]);
        if(sizeof($data->errors()) == 0) {

            $credential = request()->only(['phone', 'activation_code']);
            $user = User::query()->with('image')->where($credential)->first();
            if($user == null){
                return messages::error_output(trans('errors.wrong_otp'),401);
            }else {
                if($user->block == 1){
                    return messages::error_output(trans('errors.your_account_is_blocked'));
                }
                $token = Auth::guard('api')->login($user);
                $role = roles::query()->find($user->role_id);
                if($role->name != request('app')){
                    return messages::error_output(trans('errors.you_cant_access_this_app'));
                }
                if(request()->filled('device_id') || request()->filled('remember_token')){
                    user_devices::query()->create([
                        'user_id'=>$user->id,
                        'device_id'=>request('device_id') ?? null,
                        'notification_token'=>request('notification_token') ?? null,
                    ]);
                }
                $this->externalSmsService->send($user->phone,'رقم التحقق هو '.$user->activation_code);
                $user->save();
                $user['role'] = $role;
                if($user->email == '' && $user->username == '') {
                    $user['new_user'] = true;
                }else{
                    $user['new_user'] = false;
                }
                $user['token'] =  $token;
                $user['default_address'] = DefaultAddress::get($user->id);


                return messages::success_output('',UserResource::make($user),[
                    'new_user'=>$user['new_user']
                ]);
            }
        }else{
            return messages::error_output($data->errors());
        }
    }

    public function logout_api(){
        // Get the JWT token from the request header.
        if(request()->hasHeader('token')) {
            $token = request()->header('token');
            request()->headers->set('token', (string)$token, true);
            request()->headers->set('Authorization', 'Bearer ' . $token, true);
            try {
                $token = JWTAuth::parseToken();
                $user = $token->authenticate();

                if ($user == false) {
                    return messages::error_output(['invalid credential']);
                }
                if(request()->has('device_id')) {
                    user_devices::query()
                        ->where('user_id', '=', $user->id)
                        ->where('device_id', '=', request('device_id'))->delete();
                }

            } catch (\Exception $e) {
                return messages::error_output([$e->getMessage()]);
            }
            JWTAuth::invalidate($token);
        }

        return messages::success_output('logout successfully');

    }

    public function check_otp(usersFormRequest $formRequest){
        $data = $formRequest->validated();

        $user = User::query()
            ->where('phone',$data['phone'])
            ->where('activation_code',$data['otp'])->first();
        if($user != null){
            $user->activation_status = 1;
            $user->save();
            return messages::success_output(trans('messages.activation_done'),$user);
        }else{
            return messages::error_output(trans('errors.incorrect_otp'),401);
        }
    }

    public function user(){
        return auth()->user();
    }

}
