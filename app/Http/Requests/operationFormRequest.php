<?php

namespace App\Http\Requests;

use App\Services\DB_connections;
use Illuminate\Foundation\Http\FormRequest;

class operationFormRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $input = $this->all();

        // Manipulate the data here
        if(!is_array($input['data'])) {
            $input['data'] = json_decode($input['data'], true);
        }
        if(!is_array($input['period'])) {
            $input['period'] = json_decode($input['period'],true);
        }
        $this->replace($input);
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
            'status'=>'required',
            'branch_id'=>'required|exists:branches,id',
            'direction'=>'required',
            'period'=>'required',
            'period.*'=>'required',
            'data'=>'required|array|max:2',
            'data.*'=>'required',
            'data.*.website_id'=>'required|exists:project_websites_infos,id',
            'data.*.connection'=>'required',
            'data.*.structure'=>'filled|array',
            'data.*.structure.*'=>'filled',
            'data.*.table'=>'filled',
            'data.*.api_url_table'=>'filled',
            'data.*.tables_parameters'=>'filled|array',
            'data.*.tables_parameters.*'=>'filled|array',
            'data.*.api_url_columns'=>'filled',
            'data.*.columns'=>'filled|array',
            'data.*.columns.*'=>'filled',
            'condition_column_name'=>'filled|array',
            'condition_column_name.*'=>'filled',
            'condition_first_select'=>'filled|array',
            'condition_first_select.*'=>'filled',
            'condition_first_input'=>'filled|array',
            'condition_first_input.*'=>'filled',
            'condition_remaining_cond'=>'filled|nullable',
            'condition_remaining_cond.*'=>'nullable',
            'condition_second_select'=>'filled|nullable',
            'condition_second_select.*'=>'nullable',
            'condition_second_input'=>'filled|nullable',
            'condition_second_input.*'=>'nullable',
            'condition_query'=>'filled',
            'website_id_where_columns'=>'filled',

        ];
    }

    public function attributes()
    {
        return [
          'name'=>trans('keywords.name'),
          'branch_id'=>trans('keywords.branch'),
          'direction'=>trans('keywords.direction'),
          'period'=>trans('keywords.repeat_period'),
          'type'=>trans('keywords.period_type'),
          'data.connection'=>trans('keywords.connection_type'),
          'data.manual_or_url_tables'=>trans('keywords.manual_or_url'),
          'data.manual_or_url_columns'=>trans('keywords.manual_or_url'),
          'data.table'=>trans('keywords.tables'),
          'data.api_urls'=>trans('keywords.api_urls'),
          'data.tables_parameters'=>trans('keywords.parameters'),
        ];
    }
}
