<?php
namespace App\Http\traits;

trait messages {
    static function errors($data){
        $errors = [];
        foreach($data as $key => $val){
            $errors[$key] = $val;
        }
        return $errors;
    }


    static function error_output($errors , $status = 400 , $code = 0){
        // 422 status code
        return response()->json(['errors'=>$errors,'status'=>$status,'code'=>$code],$status);
    }


    static function success_output($msg,$data = null,$related = null){
        return response()->json(['message'=>$msg,'status'=>200,'data'=>$data,'related'=>$related]);
    }

}
