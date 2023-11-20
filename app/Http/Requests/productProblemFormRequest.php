<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class productProblemFormRequest extends FormRequest
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
            'message'=>'required',
        ];
    }

    public function attributes()
    {
        return [
            'product_id'=>trans('keywords.product'),
            'message'=>trans('keywords.message')
        ];
    }
}
