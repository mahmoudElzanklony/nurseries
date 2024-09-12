<?php

namespace App\Http\Requests;

use App\Services\DB_connections;
use Illuminate\Foundation\Http\FormRequest;

class packagesOrdersFormRequest extends FormRequest
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
            'package_id'=>'required|exists:packages,id',
        ];
    }

    public function attributes()
    {
        return [
            'package_id'=>trans('keywords.package'),
            'visa_id'=>trans('keywords.visa'),
        ];
    }
}
