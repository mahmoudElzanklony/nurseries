<?php

namespace App\Http\Controllers;

use App\Actions\MakePackageOrder;
use App\Http\Requests\bankModelFormRequest;
use App\Http\Requests\packagesOrdersFormRequest;
use App\Http\Resources\PackageResource;
use App\Http\traits\messages;
use App\Models\packages;
use App\Models\packages_orders;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
class PackagesController extends Controller
{
    use upload_image;
    // get all packages
    public function index(){
        if(request()->has('id')){
            $data = packages::query()->find(request('id'));
            if($data != null) {
                return PackageResource::make($data);
            }
        }else {
            $data = packages::query()->orderBy('id','DESC')->get();
            return PackageResource::collection($data);
        }

    }

    public function make_order(packagesOrdersFormRequest $packagesOrdersFormRequest,
                               bankModelFormRequest $modelFormRequest){
        $data = $packagesOrdersFormRequest->validated();
        // upload image if founded
        if(request()->has('image')){
            $image_file = $this->upload(request()->file('image'),'banks');
        }
        return MakePackageOrder::make_order($data,$modelFormRequest->validated(),$image_file ?? '');
    }
}
