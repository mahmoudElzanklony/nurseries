<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class categoryQuestionsFeaturesFormRequest extends FormRequest
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
            'ar_name'=>'required',
            'features'=>'nullable|array',
            'features.*'=>'nullable',
            'heading_questions'=>'nullable|array',
            'heading_questions.*'=>'nullable',
        ];
    }
}
