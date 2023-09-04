<?php


namespace App\Services\users;

use App\Models\listings_notes;

class all_listings_notes
{
    public static function get_notes(){
        return listings_notes::query()

            ->where('user_id','=',auth()->id())
            ->with('listing',function($e){
                $e->select('id','user_id','category_id','area_id','currency_id'
                    ,app()->getLocale().'_name as name',app()->getLocale().'_info as info',app()->getLocale().'_address as address',
                    'price','youtube_link','whatapp_status','contact_email_status','type')
                    ->with('images')->with('currency:id,'.app()->getLocale().'_name as name');
            })->orderBy('id','DESC')->get();
    }
}
