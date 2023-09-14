<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class governmentFormRequest extends FormRequest
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
            'ar_name'=>'required',
            'en_name'=>'filled',
        ];
    }

    public function attributes()
    {
        return [
          'ar_name'=>trans('keywords.name'),
          'en_name'=>trans('keywords.name'),
        ];
    }
}
