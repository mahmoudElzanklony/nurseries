<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class productsCareFormRequest extends FormRequest
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
            'product_id'=>'required|exists:products,id',
            'care_id'=>'required|exists:cares,id',
            'time_number'=>'required',
            'time_type'=>'required',
        ];
    }

    public function attributes()
    {
        return [
          'product_id'=>trans('keywords.product'),
          'care_id'=>trans('keywords.care'),
          'time_number'=>trans('keywords.time_number'),
          'time_type'=>trans('keywords.time_type'),
        ];
    }
}
