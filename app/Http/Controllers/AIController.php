<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductQuestionResource;
use App\Http\traits\messages;
use App\Http\traits\upload_image;
use App\Models\ai_questions;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Orhanerday\OpenAi\OpenAi;
use Illuminate\Support\Facades\File;


class AIController extends Controller
{
    //
    use messages;
    public function index(){


        $this->delete_ai_images();
        // Create a new OpenAI client.
        $client = new OpenAi(env('openai'));
        //$prompt = "an image of a well-designed space, enriched with a variety of indoor plants suitable for any indoor setting, be it a home, office, or commercial area. Feature an assortment of plants like tall, leafy fiddle leaf figs for corners, small succulents for desks or shelves, and cascading spider plants for hanging planters. Include peace lilies and rubber plants, known for their air-purifying qualities, placed in strategic locations to enhance air quality and add a refreshing touch. The arrangement of the plants should be aesthetically pleasing, with a mix of sizes, textures, and colors, creating a vibrant yet harmonious environment. This imagery should capture the essence of integrating greenery into various indoor spaces, showcasing how plants can transform any area into a more inviting and lively place.";
         $prompt = "Place various  plants ";
        if(request()->has('questions')){
            foreach(request('questions') as $q){
                $question = ai_questions::query()->find($q['id']);

                if($question != null){
                    $prompt .= 'and '.$question->en_name.' is '.$q['answer'].' ';
                    if($question->en_name == 'plant place'){
                        if($q['answer'] == 'outside house'){
                            $trees = ['Shade trees ','Heat tolerant trees ','Evergreen trees ','Ornamental trees and plants ','Flowering trees ','Deciduous trees ','Fruit trees '
                                ,'Seasonal flowers and plants ','Fragrant trees and plants ','Aromatic plants ','Flowering plants ','Climbing plants ','Rare plants '];

                            $prompt .= 'outdoor plants and i want to select plants from these plants collection ( '.implode(' , ',$trees).' ) ';
                        }else{
                            $trees = ['Hyacinthus orientalis','Aloe barbadensis','Lucky Bamboo','Kalanchoe blossfeldiana','Gardenia Jasminoides','Hedera','Scindapsus'
                                ,'Ficus elastica Robusta','agave desmettiana','Ficus elastica Robusta','Rosa','Caladium','Mini Monstera','Epipremnum aureum'];

                            $prompt .= 'indoor plants and i want to select plants from these plants collection ( '.implode(' , ',$trees).' ) ';
                        }
                    }
                }
            }
        }
        $prompt .= " plants strategically around the room without any cropping or cutting, avoiding any modifications to the existing furniture. Enrich the environment with tall fiddle leaf figs in corners, small succulents on desks or shelves, and cascading spider plants in hanging planters. Ensure that the plants complement the current furniture layout.";

        if(request()->filled('prompt')){
            $prompt = request('prompt');
        }



        try {
            $realPath = request()->file('image')->getRealPath();
            return $this->stability_ai($prompt,
                file_get_contents($realPath),
                null
            );
        }catch (\Exception $e){
            //dd($e->getMessage());
            return messages::error_output($e);
        }

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

    public function delete_ai_images()
    {
        $files = $files = File::files('public/images/ai');
        // Delete each file
        foreach ($files as $file) {
            File::delete($file);
        }
    }

    public function stability_ai($prompt,$original,$mask)
    {

        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . env('stability_ai')
        ];
        if($mask != null) {
            $url = 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/image-to-image/masking';
            $body = [
                "mask_source" => "MASK_IMAGE_BLACK",
                //"image_strength"=> 0.35,
                //"init_image_mode"=> 'IMAGE_STRENGTH',
                "text_prompts[0][text]" => $prompt,
                "cfg_scale" => 7,
                "clip_guidance_preset" => "FAST_BLUE",
                "sampler" => "K_DPM_2_ANCESTRAL",
                "samples" => 10,
                "steps" => 30
            ];

            $response =  Http::withHeaders($headers)
                ->attach('init_image', $original)
                ->attach('mask_image', $mask)
                ->post($url,$body);
        }else{
            $url = 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/image-to-image';
            $body = [
                "image_strength"=> 0.45,
                "init_image_mode"=> "IMAGE_STRENGTH",
                "text_prompts[0][text]" => $prompt,
                "cfg_scale" => 15,
                "clip_guidance_preset" => "FAST_BLUE",
               // "sampler" => "K_DPM_2_ANCESTRAL",
                "samples" => 6,
                "steps" => 40
            ];
            $response =  Http::withHeaders($headers)
                ->attach('init_image', $original)
                ->post($url,$body);
        }




        try {
            foreach ($response->json()['artifacts'] as $key => $img) {
                $image = $img['base64'];  // your base64 encoded
                $file = base64_decode($image);
                $safeName = rand(0, 10000000) . 'ai.' . 'png';
                $success = file_put_contents(public_path() . '/images/ai/' . $safeName, $file);

                $result[$key] = ['url' => url('/') . '/images/ai/' . $safeName];
            }
            return $result;
        }catch (\Exception $e){
            return messages::error_output($response->json()['message'] ?? $response->json());
        }


    }
}
