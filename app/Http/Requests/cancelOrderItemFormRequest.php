<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class cancelOrderItemFormRequest extends FormRequest
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
            'order_item_id'=>'required|exists:orders_items,id',
            'product_id'=>'required|exists:products,id',
            'content'=>'required',
        ];
    }
}
