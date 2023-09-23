<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class articleFormRequest extends FormRequest
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
        if(str_contains($this->getRequestUri(),'save')){
            return [
                'id'=>'filled',
                'article_id'=>'required|exists:articles,id',
                'comment'=>'filled',
            ];
        }else {
            return [
                'id'=>'filled',
                'title' => 'required',
                'description' => 'required',
            ];
        }
    }

    public function attributes()
    {
        return [
          'title'=>trans('keywords.name'),
          'description'=>trans('keywords.info'),
        ];
    }
}
