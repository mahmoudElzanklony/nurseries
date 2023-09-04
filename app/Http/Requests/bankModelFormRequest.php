<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class bankModelFormRequest extends FormRequest
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
            'receiver_id'=>'filled',
            'name'=>'filled|max:191',
            'date_of_transfer'=>'filled|date',
            'number_sent_from'=>'filled|numeric',
            'number_sent_to'=>'filled|numeric',
            'image'=>'filled|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }

    public function attributes()
    {
        return [
            'type_of_transfer'=>trans('keywords.type_of_transfer'),
            'receiver_id'=>trans('keywords.receiver'),
            'name'=>trans('keywords.name'), // bank name
            'date_of_transfer'=>trans('keywords.date_of_transfer'),
            'number_sent_from'=>trans('keywords.number_sent_from'),
            'number_sent_to'=>trans('keywords.number_sent_to'),
            'image'=>trans('keywords.image'),
        ];
    }
}
