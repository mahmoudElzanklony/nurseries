<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class financialReconciliationFormRequest extends FormRequest
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
            'orders_ids'=>'required|array',
            'orders_ids.*'=>'required',
        ];
    }

    public function attributes()
    {
        return [
          'orders_ids'=>trans('keywords.orders'),
        ];
    }
}
