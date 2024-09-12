<?php

namespace App\Http\Controllers;

use App\Models\orders;
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
                    "reference" => "1881",
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
        $order_id = request('merchantReference');
        if ($this->isSaleTransactionSuccess($response)) {
            //success
            orders::query()->find($order_id)->update(['is_draft'=>0]);
            return response()->json([
                'transaction_status'=>1,
                'order_id'=>$order_id,
            ]);
        }
        return redirect()->to('/noon_payment_response_failure');
        // cancel
        return response()->json([
            'transaction_status'=>0,
            'order_id'=>$order_id,
        ]);
    }

    public function failure()
    {
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
