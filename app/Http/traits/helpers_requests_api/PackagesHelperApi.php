<?php


namespace App\Http\traits\helpers_requests_api;


use App\Http\Requests\packagesFormRequest;
use App\Http\traits\messages;
use App\Models\packages;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
trait PackagesHelperApi
{
    use upload_image;
    public function save_package(Request $request , packagesFormRequest $formRequest){
        $data = $formRequest->validated();
        if($request->has('image')){
            $image_name = $this->upload(request()->file('image'),'packages');
            $data['image'] = $image_name;
        }
        packages::query()->updateOrCreate([
            'id'=>array_key_exists('id',$data) ? $data['id']:null,
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'));
    }
}
