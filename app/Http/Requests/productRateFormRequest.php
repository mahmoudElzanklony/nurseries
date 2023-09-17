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
           'product_id'=>'filled|exists:products,id',
           'order_id'=>'filled|exists:orders,id',
           'comment'=>'required',
           'rate_product_info'=>'required',
           'rate_product_services'=>'required',
           'rate_product_delivery'=>'required',
        ];
    }

    public function attributes()
    {
        return [
          'product_id'=>trans('keywords.product'),
          'comment'=>trans('keywords.comment'),
          'rate_product_info'=>trans('keywords.rate_product_info'),
          'rate_product_services'=>trans('keywords.rate_product_services'),
          'rate_product_delivery'=>trans('keywords.rate_product_delivery'),
        ];
    }
}
