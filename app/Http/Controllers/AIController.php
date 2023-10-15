<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductQuestionResource;
use App\Http\traits\messages;
use App\Models\ai_questions;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Orhanerday\OpenAi\OpenAi;


class AIController extends Controller
{
    //
    public function index(){

        // Create a new OpenAI client.
        $client = new OpenAi(env('openai'));

        // Load the input image using Intervention Image
        $file = request()->file('image');
        $name = rand(0,999999).'.png';

        $mask = request()->file('mask');
        $mask_name = rand(0,999999).'.png';

        Image::make($file)
            ->save(public_path('images/ai/').$name);

        Image::make($mask)
            ->save(public_path('images/ai/').$mask_name);



        $response = $client->imageEdit([
            "image" => curl_file_create(public_path('images/ai/'.$name)),
            "mask" => curl_file_create(public_path('images/ai/'.$mask_name)),
            "prompt" => "add a variety of lush and vibrant plants, each with unique colors, shapes, and sizes. Ensure that the plants are seamlessly integrated into the scene and appear natural in their placement. The overall composition should evoke a sense of tranquility and aesthetics. Please include a diverse selection of plants to offer the client a range of options. The lighting should be soft and natural, complementing the overall ambiance. The final image should be of the highest quality, suitable for presentation to the client for their selection. Be sure to pay attention to detail and realism",
            "n" => 8,
            "size" => "1024x1024",
        ]);



        return messages::success_output('',json_decode($response));
    }

    public function ai_questions(){
        $data =  ai_questions::query()->with('options')->get();
        return ProductQuestionResource::collection($data);
    }
}
