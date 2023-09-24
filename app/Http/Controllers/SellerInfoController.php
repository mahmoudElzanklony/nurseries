<?php

namespace App\Http\Controllers;

use App\Actions\ImageModalSave;
use App\Http\Requests\SellerInfoFormRequest;
use App\Http\traits\messages;
use App\Models\users_bank_info;
use App\Models\users_commercial_info;
use App\Models\users_store_info;
use Illuminate\Http\Request;
use App\Http\traits\upload_image;
class SellerInfoController extends Controller
{
    //
    use upload_image;

    public function save_store(SellerInfoFormRequest $request){
        $data = $request->validated();
        $output = users_store_info::query()->updateOrCreate([
            'user_id'=>auth()->id()
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'),$output);
    }

    public function save_commercial_infos(SellerInfoFormRequest $request){
        $data = $request->validated();
        $output = users_commercial_info::query()->updateOrCreate([
            'user_id'=>auth()->id()
        ],$data);
        if(request()->hasFile('images')){
            foreach(request()->file('images') as $file){
                $image = $this->upload($file,'sellers_commercial');
                if($image){
                    ImageModalSave::make($output->id,'users_commercial_info','sellers_commercial/'.$image);
                }
            }
        }
        return messages::success_output(trans('messages.saved_successfully'),$output);
    }

    public function save_bank(SellerInfoFormRequest $request){
        $data = $request->validated();
        $output = users_bank_info::query()->updateOrCreate([
            'user_id'=>auth()->id()
        ],$data);
        return messages::success_output(trans('messages.saved_successfully'),$output);
    }


}
