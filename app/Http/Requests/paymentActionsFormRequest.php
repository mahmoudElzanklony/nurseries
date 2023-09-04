<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class paymentActionsFormRequest extends FormRequest
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
            //
            'money'=>'required|numeric',
            'payment_type'=>'required',
            'type_of_transfer'=>'required',
            'info_report_about_success_payment'=>'required',
        ];
    }

    public function attributes()
    {
        return [
            'money'=>trans('keywords.money'),
            'type_of_transfer'=>trans('keywords.type_of_transfer'),
        ];
    }
}
