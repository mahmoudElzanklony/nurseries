<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class customOrderClientReplyFormRequest extends FormRequest
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
            'custom_orders_seller_id'=>'required',
            'selected_items'=>'required|array',
            'selected_items.*.id'=>'required|exists:custom_orders_sellers_replies,id',
            'selected_items.*.quantity'=>'required|min:1',
            'payment'=>'filled'
        ];
    }

    public function attributes()
    {
        return [
          'visa_id'=>trans('keywords.visa'),
        ];
    }
}
