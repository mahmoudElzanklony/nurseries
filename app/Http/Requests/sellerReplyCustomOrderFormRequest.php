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
            'custom_order_id'=>'required|exists:custom_orders_sellers,id',
            'items'=>'required|array',
            'items.*.id'=>'filled',
            'items.*.name'=>'required',
            'items.*.info'=>'required',
            'items.*.product_price'=>'required',
            'items.*.delivery_price'=>'required',
            'items.*.days_delivery'=>'required',
            'items.*.quantity'=>'required|min:1',
            'items.*.images'=>'filled|array',
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
