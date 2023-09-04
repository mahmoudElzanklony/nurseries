<?php

namespace App\Http\Requests;

use App\Services\DB_connections;
use Illuminate\Foundation\Http\FormRequest;

class branchFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        DB_connections::get_wanted_tenant_user();

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
            'id'=>'nullable',
            'project_id'=>'required|exists:projects,id',
            'name'=>'required',
            'status'=>'filled',
        ];
    }

    public function attributes()
    {
        return [
            'project_id'=>trans('keywords.project'),
            'name'=>trans('keywords.name'),
        ];
    }
}
