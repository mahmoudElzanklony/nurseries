<?php


namespace App\Actions;


use App\Models\products_delivery;
use GuzzleHttp\Client;

class CheckPlaceMapLocation
{
    public static function check_delivery($product_id,$default_address){
        $deliveries = products_delivery::query()->with('city')
            ->where('product_id','=',$product_id)->get();
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
        foreach ($response['results'] as $result) {
            if(isset($result['types']) && in_array('administrative_area_level_2',$result['types'])){
                $place_id = $result['place_id'];
                $cities_codes = $deliveries->map(function ($e){
                    return $e->city->map_code;
                })->toArray();
                if(!(in_array($result['place_id'],$cities_codes))){
                    return false;
                }
            }
        }
        if($place_id == null){
            return false;
        }
        foreach($deliveries as $delivery){
            if($delivery->city->map_code == $place_id){
                return $delivery;
                break;
            }
        }
        return false;
    }
}
