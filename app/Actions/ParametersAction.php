<?php


namespace App\Actions;



use App\Models\parameters;

class ParametersAction
{
    public static function save_parameter($related,$parameter_data){
        $parameter_obj = new parameters();
        $parameter_obj->p_key = $parameter_data['p_key'];
        $parameter_obj->p_value = $parameter_data['p_value'];
        if($parameter_obj->p_key == null || $parameter_obj->p_value == null){
            return;
        }
        if(array_key_exists('id',$parameter_data)){
            $parameter_obj->id = $parameter_data['id'];
            $parameter_check_db = parameters::query()->find($parameter_data['id']);
            if($parameter_check_db != null){
                $parameter_check_db->update($parameter_obj->toArray());
            }

        }else {
            $related->parameters()->save($parameter_obj);
        }
    }
}
