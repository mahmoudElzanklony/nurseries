<?php


namespace App\Services\users;


use App\Models\listing_statistics;
use App\Models\listings_info;

class listings_statistics_data
{
    public static function get_data(){
        return listing_statistics::query()->whereHas('listing',function($listing){
            $listing->where('user_id','=',auth()->id());
        })->selectRaw('sum(no_search) as total_search,sum(no_seen) as total_no_seen,sum(no_contact_tried) as total_no_contact_tried ,count(listing_id) as no_listings')->first();
    }
}
