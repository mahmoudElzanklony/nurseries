<?php


namespace App\Interfaces;


interface IPayment
{
    public function handle($data);
}
