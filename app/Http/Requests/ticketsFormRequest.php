<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ticketsFormRequest extends FormRequest
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
            'ticket_cat_id'=>'required|exists:tickets_categories,id',
            'title'=>'required|max:191',
            'message'=>'required',
        ];
    }

    public function attributes()
    {
        return [
          'ticket_cat_id'=>trans('keywords.category'),
          'title'=>trans('keywords.title'),
          'message'=>trans('keywords.message'),
        ];
    }
}
