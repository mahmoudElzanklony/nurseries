<?php


namespace App\Repositories;


use App\Models\operation_condition_query;
use App\Models\operation_conditions;
use App\Models\operation_repeat;
use App\Models\operations;
use App\Models\website_operation_database_tables_columns;
use Illuminate\Support\Facades\DB;

class OperationRepository
{
    private $operation;
    private $period;

    // create or update operation
    public function create_init_operation($data){
        DB::beginTransaction();
        $inial_data = [
            'name'=>$data['name'],
            'branch_id'=>$data['branch_id'],
            'direction'=>$data['direction'],
            'status'=>$data['status']
        ];
        // create or update operation
        $result = operations::query()->updateOrCreate([
            'id'=>array_key_exists('id',$data) ? $data['id']:null,
        ],$inial_data);

        $this->operation = $result;
        // prepare data for insert at save period
        $this->period = $data['period'];


        $check = website_operation_database_tables_columns::query()
            ->where('type','=','column')
            ->where('operation_id','=',$this->operation->id)->get();
        if(sizeof($check) > 0){
            website_operation_database_tables_columns::query()
                ->where('type','=','column')
                ->where('operation_id','=',$this->operation->id)->delete();
        }
        foreach ($data['data'] as $item){
            if($item['connection'] === 'database'){
                $this->create_db_tables_columns($item);
            }else{

            }
        }
        $this->save_period();
        $this->save_conditions($data);
    }

    public function create_db_tables_columns($data){


        foreach ($data['structure'] as $item){
            if(is_array($item['value'])){
                // columns will be added
                foreach($item['value'] as $column){
                    // each column will be added
                    website_operation_database_tables_columns::query()->create([
                        'operation_id'=>$this->operation->id,
                        'website_id'=>$data['website_id'],
                        'type'=>'column',
                        'name'=>$column['value']
                    ]);
                }
            }else{
                // table will be added
                website_operation_database_tables_columns::query()->updateOrCreate([
                    'operation_id'=>$this->operation->id,
                    'website_id'=>$data['website_id'],
                    'type'=>'table',
                ],[
                    'operation_id'=>$this->operation->id,
                    'website_id'=>$data['website_id'],
                    'type'=>'table',
                    'name'=>$item['value']
                ]);
            }
        }
        return $this;
    }

    public function save_period(){
        foreach($this->period as $p) {
            $p['operation_id'] = $this->operation->id;
            operation_repeat::query()->updateOrCreate([
                'id' => array_key_exists('id',$p) ? $p['id']:null,
            ], $p);
        }
        // DB::commit();
    }

    public function save_conditions($data){
        if(sizeof($data['condition_column_name']) > 0){
            $delete_cond = operation_conditions::query()
                ->where([
                    'operation_id'=>$this->operation->id,
                    'website_id'=>$data['website_id_where_columns'] ?? null,
                ])->first();
            $delete_query = operation_condition_query::query()
                ->where([
                    'operation_id'=>$this->operation->id,
                    'website_id'=>$data['website_id_where_columns'] ?? null,
                ])->first();
            if($delete_cond != null){
                $delete_cond->delete();
            }
            if($delete_query != null){
                $delete_query->delete();
            }
            foreach($data['condition_column_name'] as $key => $p) {
                operation_conditions::query()->updateOrCreate([
                    'operation_id'=>$this->operation->id,
                    'website_id'=>$data['website_id_where_columns'] ?? null,
                    'column_name'=>$data['condition_column_name'][$key] ?? null,
                    'first_select'=>$data['condition_first_select'][$key] ?? null,
                    'first_input'=>$data['condition_first_input'][$key] ?? null,
                    'remaining_cond'=>$data['condition_remaining_cond'][$key] ?? null,
                    'second_select'=>$data['condition_second_select'][$key] ?? null,
                    'second_input'=>$data['condition_second_input'][$key] ?? null,
                ],[
                    'operation_id'=>$this->operation->id,
                    'website_id'=>$data['website_id_where_columns'] ?? null,
                ]);
            }
            operation_condition_query::query()->updateOrCreate([
                'where_query'=>$data['condition_query']
            ],[
                'operation_id'=>$this->operation->id,
                'website_id'=>$data['website_id_where_columns'] ?? null,
            ]);
        }

        DB::commit();
    }



}
