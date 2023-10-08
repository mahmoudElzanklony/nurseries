<?php


namespace App\Http\traits;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait upload_image
{
    public function upload($file,$folder_name,$type = 'one'){
        $valid_extensions = ['png','jpg','jpeg','gif'];
        if($type == 'one') {
            if (in_array(strtolower($file->getClientOriginalExtension()), $valid_extensions)) {
                $name = time().rand(0,9999999999999). '_image.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/' . $folder_name), $name);
                return $name;
            } else {
                return false;
            }
        }
    }

    public function download_and_save($url,$folder){
        $image = Image::make($url);
        $name = time().rand(0,9999999999999). '_image.png';
        // Save the image to the public folder
        $image->save(public_path('images/'.$folder.'/').$name);
        return $name;
    }
}
