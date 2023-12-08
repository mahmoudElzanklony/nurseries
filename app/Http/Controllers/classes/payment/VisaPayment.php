<?php


namespace App\Http\Controllers\classes\payment;


use App\Interfaces\IPayment;
use App\Models\users_visa;

class VisaPayment implements IPayment
{

    public function handle($data)
    {
        // check visa related to this user

        $status = $this->check_payment_related_to_user($data);
        $output = [
          'status'=>$status,
          'message'=>'',
        ];
        return  $output;
    }

    public function check_payment_related_to_user($data){
        $user_visa = users_visa::query()
            ->where('user_id','=',auth()->id())
            ->where('id','=',$data['id'])->first();
        dd($user_visa);
        if($user_visa != null){
            $status = true;
        }else{
            $status = false;
        }
        return $status;
    }
}
