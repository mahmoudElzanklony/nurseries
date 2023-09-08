<?php


namespace App\Http\Controllers\classes\payment;


use App\Interfaces\IPayment;

class VisaPayment implements IPayment
{

    public function handle($data)
    {
        $output = [
          'status'=>true,
        ];
        return  $output;
    }
}
