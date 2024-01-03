<?php


namespace App\Actions;


use App\Models\products_delivery;
use GuzzleHttp\Client;

class CheckPlaceMapLocation
{
    public static function check_delivery($product_id,$default_address){
        $deliveries = products_delivery::query()->with('city')
            ->where('product_id','=',$product_id)->get();


        $cities_en_english = $deliveries->map(function($e){
            return $e['city']['en_name'];
        })->toArray();

        if(sizeof($deliveries) == 0){
            return false;
        }
        if($default_address == null){
            return false;
        }
        $client = new Client();

        $apiUrl = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$default_address->latitude.','.$default_address->longitude.'&key='.env('GOOGLE_MAPS_API_KEY');

        $client_request = $client->get($apiUrl);
        // Parse the response
        $response = json_decode($client_request->getBody(), true);
        $place_id = null;
        $result = false;
        if (isset($response['results']) && is_array($response['results'])) {
            foreach ($response['results'] as $result) {
                foreach ($result['address_components'] as $address_component) {
                    if (in_array('locality', $address_component['types']) && in_array($address_component['long_name'],$cities_en_english)) {
                        dd($deliveries[0]->city);
                        $result =  $deliveries->find(function ($e) use ($address_component){
                            dd($e);
                         //   return $e['city']['en_name'] == $address_component['long_name'];
                        });
                        break;
                    }
                }
            }
        }
        dd($result);
        return $result;
    }
}
