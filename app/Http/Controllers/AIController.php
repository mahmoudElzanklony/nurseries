<?php

namespace App\Http\Controllers;

use App\Http\traits\messages;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Orhanerday\OpenAi\OpenAi;


class AIController extends Controller
{
    //
    public function index(){

        // Create a new OpenAI client.
        $client = new OpenAi(env('openai'));

       /* // Load the input image using Intervention Image
        $file = request()->file('image');
        $name = rand(0,999999).'.png';

        $mask = Image::make($file)
            ->save(public_path('images/ai/').$name);// invert it to use as a mask

*/

        $response = $client->image([
            //"image" => curl_file_create(public_path('images/ai/'.$name)),
            "prompt" => "room plants",
            "n" => 8,
            "size" => "1024x1024",
        ]);



        return messages::success_output('',json_decode($response));
    }
}
