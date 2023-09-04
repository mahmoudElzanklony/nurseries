<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class cityFormRequest extends FormRequest
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
            'name'=>'required',
            'country_id'=>'required|exists:countries,id',
        ];
    }

    public function attributes()
    {
        return [
          'name'=>trans('keywords.name'),
          'country_id'=>trans('keywords.country'),
        ];
    }
}
