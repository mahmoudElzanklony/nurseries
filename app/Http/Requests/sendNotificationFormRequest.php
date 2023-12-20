<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sendNotificationFormRequest extends FormRequest
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
            'name'=>'filled',
            'notification_template_id'=>'filled',
            'notification_type'=>'filled',
            'user_type'=>'filled',
            'notification_type_id'=>'filled',
            'send_at'=>'filled',
            'content'=>'filled',
        ];
    }
}
