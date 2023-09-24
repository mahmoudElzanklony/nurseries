<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class customOrderFormRequest extends FormRequest
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
    public function rules()
    {
        return [
            'id'=>'filled',
            'name'=>'required',
            'sellers'=>'required|array',
            'sellers.*'=>'required',
        ];
    }
    public function attributes()
    {
        return [
          'name'=>trans('keywords.name'),
          'sellers'=>trans('keywords.sellers'),
        ];
    }
}
