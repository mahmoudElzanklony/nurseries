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
        $prompt = "add a variety of lush and vibrant plants, each with unique colors, shapes, and sizes. Ensure that the plants are seamlessly integrated into the scene and appear natural in their placement.The overall composition should evoke a sense of tranquility and aesthetics. Please include a diverse selection of plants to offer the client a range of options.The lighting should be soft and natural, complementing the overall ambiance. The final image should be of the highest quality,suitable for presentation to the client for their selection ";

        if(request()->has('questions')){
            foreach(request('questions') as $q){
                $question = ai_questions::query()->find($q['id']);
                if($question != null){
                    $prompt .= 'and '.$question->en_name.' is '.$q['answer'].' ';
                    if($question->en_name == 'plant place'){
                        if($q['answer'] == 'outside house'){
                            $trees = ['Shade trees ','Heat tolerant trees ','Evergreen trees ','Ornamental trees and plants ','Flowering trees ','Deciduous trees ','Fruit trees '
                                ,'Seasonal flowers and plants ','Fragrant trees and plants ','Aromatic plants ','Flowering plants ','Climbing plants ','Rare plants '];

                            $prompt .= 'and i want to select plants from these plants collection ( '.implode(' , ',$trees).' ) ';
                        }else{
                            $trees = ['Hyacinthus orientalis','Aloe barbadensis','Lucky Bamboo','Kalanchoe blossfeldiana','Gardenia Jasminoides','Hedera','Scindapsus'
                                ,'Ficus elastica Robusta','agave desmettiana','Ficus elastica Robusta','Rosa','Caladium','Mini Monstera','Epipremnum aureum'];

                            $prompt .= 'and i want to select plants from these plants collection ( '.implode(' , ',$trees).' ) ';
                        }
                    }
                }
            }
        }
        if(request()->filled('prompt')){
            $prompt = request('prompt');
        }
        //dd($prompt);

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
            "prompt" => $prompt,
            "n" => 10,
            "size" => "1024x1024",
        ]);



        return messages::success_output('',json_decode($response));
    }

    public function ai_questions(){
        $data =  ai_questions::query()->with('options')->get();
        return ProductQuestionResource::collection($data);
    }
}
