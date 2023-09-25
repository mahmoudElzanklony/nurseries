<?php


namespace App\Interfaces;


interface IPayment
{
    public function handle($data);

    public function check_payment_related_to_user($data);
}
