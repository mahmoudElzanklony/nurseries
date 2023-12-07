<?php


namespace App\Http\Controllers\classes\payment;


use App\Interfaces\IPayment;
use App\Models\users_visa;

class VisaPayment implements IPayment
{

    public function handle($data)
    {
        // check visa related to this user
        dd($data);
        $status = $this->check_payment_related_to_user($data);
        $output = [
          'status'=>$status,
          'message'=>'',
        ];
        return  $output;
    }

    public function check_payment_related_to_user($data){
        $id = $data['id'];

        $user_visa = users_visa::query()
            ->where('user_id','=',auth()->id())
            ->where('id','=',$id)->first();
        if($user_visa != null){
            $status = true;
        }else{
            $status = false;
        }
        return $status;
    }
}
