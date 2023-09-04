<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerInfoFormRequest extends FormRequest
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
    public function save_store(){
        return [
            'type'=>'required|max:191',
            'name'=>'required|max:191',
            'address'=>'required|max:191',
            'business_phone'=>'nullable|min:7',
            'business_email'=>'nullable|min:7',
        ];
    }

    public function save_commercial_data(){
        return [
            'commercial_register'=>'required',
            'tax_card'=>'required',
            'images'=>'required|array',
            'images.*'=>'required|image|mimes:png,jpg,jpeg,gif',
        ];
    }

    public function save_bank_info_data(){
        return [
            'owner_name'=>'required',
            'bank_name'=>'required',
            'bank_account'=>'required',
            'bank_iban'=>'required',
        ];
    }


    public function rules()
    {
        if(str_contains($this->getRequestUri(),'/save-store')) {
            return $this->save_store();
        }else if(str_contains($this->getRequestUri(),'/save-commercial-infos')) {
            return $this->save_commercial_data();
        }else if(str_contains($this->getRequestUri(),'/save-bank')) {
            return $this->save_bank_info_data();
        }
    }

    public function attributes()
    {
        return [
            'name'=>trans('keywords.name'),
            'type'=>trans('keywords.type'),
            'email'=>trans('keywords.email'),
            'address'=>trans('keywords.address'),
            'business_phone'=>trans('keywords.business_phone'),
            'business_email'=>trans('keywords.business_email'),
            'images'=>trans('keywords.images'),
            'owner_name'=>trans('keywords.owner_name'),
            'bank_name'=>trans('keywords.bank_name'),
            'bank_account'=>trans('keywords.bank_account'),
            'bank_iban'=>trans('keywords.bank_iban'),
        ];
    }

}
