<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class addressesFormRequest extends FormRequest
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
            //
            'id'=>'filled',
            'area_id'=>'required|exists:areas,id',
            'address'=>'required',
            'default_address'=>'required',
        ];
    }

    public function attributes()
    {
        return [
          'address'=>trans('keywords.address'),
        ];
    }
}
