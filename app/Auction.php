<?php

namespace App;

use App\Scheme;
use DB;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    //
    /**
     * Get the subscriber that won the bid.
     */
    public function subscriber()
    {
        return $this->belongsTo('App\Subscriber')->withDefault();
    }

    public function scheme()
    {
        return $this->belongsTo('App\Scheme')->withDefault();
    }

    public static function getAuctionsbySchemeandStartDate(Scheme $scheme)
    {


    }
}
