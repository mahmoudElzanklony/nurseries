<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class projectsFormRequest extends FormRequest
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
            'name'=>'required|max:191',
            'info'=>'required',
            'image'=>'nullable',
            "projects_connections_data"    => "required|array|max:2",
            "projects_connections_data.*"  => "required",
        ];
    }

    public function attributes()
    {
        return [
            'name'=>trans('keywords.name'),
            'info'=>trans('keywords.info'),
            'image'=>trans('keywords.image'),
            'projects_connections_data'=>trans('keywords.projects_connections_data'),
        ];
    }
}
