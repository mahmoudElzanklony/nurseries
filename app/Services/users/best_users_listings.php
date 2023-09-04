<?php


namespace App\Services\users;


use App\Models\listing_statistics;
use App\Models\listings_info;

class best_users_listings
{
    public static function best_users(){
        /*return listings_info::selection()->addSelect([
                           'total_contact'=>listing_statistics::query()
                               ->whereColumn('listings_infos.id','listing_statistics.listing_id')
                               ->selectRaw('sum(listing_statistics.no_contact_tried)')->limit(1)
                             ])
               // ->selectRaw('sum(listing_statistics.total_contact)')
                ->groupBy('user_id',function ($e){
                    $e->sum('listing_statistics.total_contact');
                })
                ->get();*/
        return listing_statistics::query()
            ->join('listings_infos','listings_infos.id','=','listing_statistics.listing_id')
            ->join('users','users.id','=','listings_infos.user_id')
            ->groupBy('listings_infos.user_id')
            ->selectRaw('sum(listing_statistics.no_seen) as total_seen ,count(listing_statistics.listing_id) as total_listings , users.*')
            ->orderBy('total_seen','DESC')->limit(5)
            ->get();
    }
}
