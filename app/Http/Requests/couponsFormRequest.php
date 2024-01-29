<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class couponsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->role->name != 'client';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'=>'filled',
            'ar_name'=>'required|max:191',
            'en_name'=>'filled',
            'code'=>'required',
            'discount'=>'required',
            'number'=>'required|numeric|min:1',
            'using_once'=>'filled',
            'status'=>'filled',
            'end_date'=>'filled',
        ];
    }

    public function attributes()
    {
        return [
          'ar_name'=>trans('keywords.name'),
          'discount'=>trans('keywords.value'),
        ];
    }
}
