<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sellerReplyCustomOrderFormRequest extends FormRequest
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
            'info'=>'required',
            'product_price'=>'required',
            'days_delivery'=>'required',
            'delivery_price'=>'required',
        ];
    }

    public function attributes()
    {
        return [
          'info'=>trans('keywords.info'),
          'product_price'=>trans('keywords.price'),
          'days_delivery'=>trans('keywords.days_delivery'),
          'delivery_price'=>trans('keywords.delivery_price'),
        ];
    }
}
