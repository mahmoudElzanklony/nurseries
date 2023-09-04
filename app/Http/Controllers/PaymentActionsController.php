<?php

namespace App\Http\Controllers;

use App\Actions\DoPaymentProcess;
use App\Actions\MakePackageOrder;
use App\Http\Requests\bankModelFormRequest;
use App\Http\Requests\paymentActionsFormRequest;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
class PaymentActionsController extends Controller
{
    //
    use upload_image;
    public function do_payment(bankModelFormRequest $modelFormRequest ,
                               paymentActionsFormRequest $actionsFormRequest){
        $data = $actionsFormRequest->validated();
        // upload image if founded
        if(request()->has('image')){
            $image_file = $this->upload(request()->file('image'),'banks');
        }
        return DoPaymentProcess::make($data,$modelFormRequest->validated(),$image_file ?? '');
    }
}
