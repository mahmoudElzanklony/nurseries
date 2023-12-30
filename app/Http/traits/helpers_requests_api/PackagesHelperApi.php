<?php


namespace App\Http\traits\helpers_requests_api;


use App\Filters\EndDateFilter;
use App\Filters\marketer\StatusFilter;
use App\Filters\marketer\UsernameFilter;
use App\Filters\StartDateFilter;
use App\Http\Requests\packageFeaturesFormRequest;
use App\Http\Requests\packagesFormRequest;
use App\Http\Resources\PackageResource;
use App\Http\Resources\UserPackageResource;
use App\Http\traits\messages;
use App\Models\packages;
use App\Models\packages_features;
use App\Models\users_packages;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
use Illuminate\Pipeline\Pipeline;

trait PackagesHelperApi
{
    use upload_image;
    public function save_package(Request $request , packagesFormRequest $formRequest){
        $data = $formRequest->validated();
        $output = packages::query()->updateOrCreate([
            'id'=>array_key_exists('id',$data) ? $data['id']:null,
        ],$data);
        if(request()->has('items')){
            foreach(request('items') as $item){
                $item['package_id'] = $output['id'];
                packages_features::query()->updateOrCreate([
                    'id'=>$item['id'] ?? null
                ],$item);
            }
        }
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

    public function packages_users(){
        $data = users_packages::query()->with(['user','package'])->orderBy('id','DESC');
        $final  = app(Pipeline::class)
            ->send($data)
            ->through([
                StartDateFilter::class,
                EndDateFilter::class,

            ])
            ->thenReturn()
            ->paginate(10);
        return UserPackageResource::collection($final);
    }

    public function packages_statistics(){
        $output = [
          'all'=>users_packages::query()->count(),
          'expired'=>users_packages::query()
              ->where('expiration_date','<',date('Y-m-d'))->count(),
          'active'=>users_packages::query()
                ->where('expiration_date','>=',date('Y-m-d'))->count(),
        ];
        return messages::success_output('',$output);
    }
}
