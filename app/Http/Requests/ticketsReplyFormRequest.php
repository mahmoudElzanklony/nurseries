<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ticketsReplyFormRequest extends FormRequest
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
            'id'=>'filled|exists:tickets_messages,id',
            'ticket_id'=>'required|exists:tickets,id',
            'message'=>'required',
        ];
    }

    public function attributes()
    {
        return [
          'ticket_id'=>trans('keywords.ticket'),
          'message'=>trans('keywords.message'),
        ];
    }
}
