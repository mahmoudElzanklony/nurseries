<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ordersFormRequest extends FormRequest
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
        if(str_contains($this->getRequestUri(),'/update-status')){
            return [
              'id'=>'required|exists:orders,id',
              'status'=>'required',
            ];
        }else {
            return [
                'seller_id' => 'required|exists:users,id',
                'items' => 'required|array',
                'items.*' => 'required',
                'payment_method' => 'filled',
                'has_coupon' => 'filled',
                'payment_data' => 'required',
            ];
        }
    }

    public function attributes()
    {
        return [
          'status'=>trans('keywords.status')
        ];
    }
}
