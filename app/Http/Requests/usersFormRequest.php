<?php

namespace App\Http\Requests;

use App\Rules\matchOldPasssword;
use Illuminate\Foundation\Http\FormRequest;

class usersFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function login(){
        return [
            'email'=>'required|email|max:191',
           // 'password'=>'required|min:6|max:191',
        ];
    }

    public function check_otp(){
        return [
            'phone'=>'required',
            'otp'=>'required',
        ];
    }

    public function register(){
        return [
            'phone'=>'filled|unique:users,phone,NULL,id,deleted_at,NULL',
            'country_code'=>'required',
            'type'=>'required',
            'email'=>'filled',
            'register_by'=>'required',
        ];
    }

    public function personal_info(){
        return [
            //
            'username'=>'required|max:191',
            'email'=>'filled|email|max:191|unique:users,email'.(auth()->check() ? ( auth()->user()->role_id == 1?','.auth()->id():','.request('id')):''),
            'phone'=>'required|min:7',
            'address'=>'filled|max:191',
            'type'=>'required',
        ];
    }
    public function update_admin(){
        return [
            //
            'country_id'=>'required|integer|exists:countries,id',
            'username'=>'required|max:191',
            'email'=>'required|email|max:191|unique:users,email,'.request('id'),
/*            'password'=>'filled|confirmed|min:7|max:191',
            'password_confirmation' => 'required| min:7',*/
            'phone'=>'required|min:7',
            'address'=>'nullable|max:191',
            'block'=>'required',
            'auto_publish'=>'required',
            'image'=>'nullable|image|mimes:jpg,jpeg,png,gif',
        ];
    }

    public function update_email_image(){
        return [
            //
            'email'=>'required|max:191|email|unique:users,email,'.auth()->user()->id,
            'profile_picture'=>'nullable|mimes:jpg,jpeg,png,gif',
        ];
    }

    public function update_password(){
        return [
            'current_password'=>['required',new matchOldPasssword()],
            'password'=>'required|confirmed|min:7|max:191',
        ];
    }

    public function reset_psasword(){
        return [
            'password'=>'required|confirmed|min:7|max:191',
        ];
    }

    public function update_personal_data(){
        return [
            'id'=>'filled',
            'username'=>'required|max:191',
            'email'=>'required|max:191|email|unique:users,email,'.auth()->user()->id,
            'phone'=>'filled|min:7',
            'address'=>'filled',
        ];
    }


    public function rules()
    {
        if(str_contains($this->getRequestUri(),'/login')){
            return $this->login();
        }if(str_contains($this->getRequestUri(),'check-otp')){
        return $this->check_otp(); //
        }if(str_contains($this->getRequestUri() , '/register')){
        return $this->register();
        }else if(str_contains($this->getRequestUri() , '/update-personal') ){
            return $this->update_personal_data();
        }else if(str_contains($this->getRequestUri() , '/profile/update-password')){
            return $this->update_password();
        }else if(str_contains($this->getRequestUri() , '/newpass')){
            return $this->reset_psasword();
        }else if(str_contains($this->getRequestUri() , '/dashboard')){
            return $this->update_admin();
        }
    }

    public function attributes()
    {
        return [
            'city_id'=>trans('keywords.city_id'),
            'username'=>trans('keywords.username'),
            'email'=>trans('keywords.email'),
            'password'=>trans('keywords.password'),
            'current_password'=>trans('keywords.current_password'),
            'address'=>trans('keywords.address'),
            'phone'=>trans('keywords.phone'),
            'image'=>trans('keywords.image'),
            'profile_picture'=>trans('keywords.image'),

            'block'=>trans('keywords.block'),
        ];
    }

}
