<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class visaFormRequest extends FormRequest
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
            'name'=>'required',
            'card_number'=>'required|numeric|digits:16',
            'end_date'=>'required',
            'cvv'=>'required|numeric|digits:3',
        ];
    }

    public function attributes()
    {
        return [
          'name'=>trans('keywords.name'),
          'card_number'=>trans('keywords.card_number'),
          'end_date'=>trans('keywords.end_date'),
          'cvv'=>trans('keywords.cvv'),
        ];
    }
}
