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
            'visa_id'=>'required|exists:users_visa,id',
        ];
    }

    public function attributes()
    {
        return [
          'visa_id'=>trans('keywords.visa'),
        ];
    }
}
