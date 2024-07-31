<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CodeBugLab\NoonPayment\NoonPayment;

class NoonPaymentController extends Controller
{

    public function index()
    {
        if(request()->filled('amount') && request()->filled('name')){
            $amount = request('amount');
            $name = request('name');

            $response = NoonPayment::getInstance()->initiate([
                "order" => [
                    "reference" => "1",
                    "amount" => $amount,
                    "currency" => "SAR",
                    "name" => $name,
                ],
                "configuration" => [
                    "locale" => "en"
                ]
            ]);


            if ($response->resultCode == 0) {
                return redirect($response->result->checkoutData->postUrl);
            }

            return $response;
        }else{
            abort(401,'amount and name should be passed through request');
        }

    }

    public function response(Request $request)
    {
        $response = NoonPayment::getInstance()->getOrder($request->orderId);

        if ($this->isSaleTransactionSuccess($response)) {
            //success
            return response()->json([
                'transaction_status'=>1,
            ]);
        }

        // cancel
        return response()->json([
            'transaction_status'=>0,
        ]);
    }

    private function isSaleTransactionSuccess($response)
    {
        return isset($response->result->transactions) &&
            is_array($response->result->transactions) &&
            $response->result->transactions[0]->type == "SALE" &&
            $response->result->transactions[0]->status == "SUCCESS";
    }
}
