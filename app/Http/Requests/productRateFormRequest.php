<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class productRateFormRequest extends FormRequest
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
           'comment'=>'required',
           'rate'=>'required',
        ];
    }

    public function messages()
    {
        return [
          'product_id'=>trans('keywords.product'),
          'comment'=>trans('keywords.comment'),
          'rate'=>trans('keywords.rate'),
        ];
    }
}
