<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\Requests\packageFeaturesFormRequest;
use App\Http\Requests\packagesFormRequest;
use App\Http\Resources\PackageResource;
use App\Http\traits\messages;
use App\Models\packages;
use App\Models\packages_features;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
trait PackagesHelperApi
{
    use upload_image;
    public function save_package(Request $request , packagesFormRequest $formRequest){
        $data = $formRequest->validated();
        $output = packages::query()->updateOrCreate([
            'id'=>array_key_exists('id',$data) ? $data['id']:null,
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'),PackageResource::make($output));
    }

    public function save_package_features(packageFeaturesFormRequest $request){
        $data = $request->validated();
        foreach(request('items') as $item){
            $item['package_id'] = $data['package_id'];
            packages_features::query()->updateOrCreate([
                'id'=>$item['id'] ?? null
            ],$item);
        }
        $output = packages::query()->with('features')->find($data['package_id']);
        return messages::success_output(trans('messages.saved_successfully'),PackageResource::make($output));
    }
}
