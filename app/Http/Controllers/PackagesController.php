<?php

namespace App\Http\Controllers;

use App\Actions\MakePackageOrder;
use App\Actions\PaymentModalSave;
use App\Filters\EndDateFilter;
use App\Filters\IdFilter;
use App\Filters\NameOnlyFilter;
use App\Filters\orders\ClientNameFilter;
use App\Filters\orders\MaxPriceFilter;
use App\Filters\orders\MinPriceFilter;
use App\Filters\orders\StatusOrderFilter;
use App\Filters\StartDateFilter;
use App\Filters\UserIdFilter;
use App\Http\Controllers\classes\payment\VisaPayment;
use App\Http\Requests\packagesOrdersFormRequest;
use App\Http\Resources\PackageResource;
use App\Http\Resources\UserPackageOrderResource;
use App\Http\traits\messages;
use App\Models\packages;
use App\Models\users_packages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
use Illuminate\Pipeline\Pipeline;

class PackagesController extends Controller
{
    use upload_image;
    // get all packages
    public function index(){
        $data = packages::query()->when(request()->has('type'),function ($e){
            $e->where('type','=',request('type'));
        })->withCount('users')->with('features')->orderBy('id','DESC');
        $final = app(Pipeline::class)
            ->send($data)
            ->through([
                NameOnlyFilter::class
            ])
            ->thenReturn()
            ->orderBy('id','DESC')
            ->paginate(10);
        return PackageResource::collection($final);

    }

    public function my_package(){
        $output = users_packages::query()->where('user_id','=',auth()->id())->with('package')->first();
        return UserPackageOrderResource::make($output);
    }

    public function make_order(packagesOrdersFormRequest $packagesOrdersFormRequest){
        $data = $packagesOrdersFormRequest->validated();

        $visa_obj = new VisaPayment();
        /*$visa_info = [
            'id'=>$data['visa_id']
        ];*/
        //$validate_visa = $visa_obj->handle($visa_info);
        if(true){
            // handle object of data to be inserted

            $data = $this->handle_package($data);
            $order = users_packages::query()->updateOrCreate([
                'user_id'=>auth()->id()
            ],$data);
            PaymentModalSave::make($order->id,'users_packages',$data['price']);
            return messages::success_output(trans('messages.saved_successfully'),UserPackageOrderResource::make($order));
        }else{
            return messages::error_output($validate_visa['message']);
        }

    }

    public function handle_package($data){
        $package = packages::query()->find($data['package_id']);
       // unset($data['visa_id']);
        $data['price'] = $package->price;

        $data['expiration_date'] = $package->type == 'month' ? Carbon::now()->addMonths(1) : Carbon::now()->addYears(1);
        $data['user_id'] = auth()->id();
        return $data;
    }

}
