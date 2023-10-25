<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class packagesFormRequest extends FormRequest
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
            'description'=>'required',
            'price'=>'required',
            'type'=>'required',
        ];
    }

    public function attributes()
    {
        return [
          'name'=>trans('keywords.name'),
          'description'=>trans('keywords.info'),
          'no_points'=>trans('keywords.no_points'),
          'price'=>trans('keywords.price'),
          'type'=>trans('keywords.type'),
        ];
    }
}
