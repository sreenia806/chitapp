<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scheme extends Model
{
    use SoftDeletes;
    //
    protected $fillable = ['title', 'duration', 'subscribers', 'installment', 'value'];

    /**
     * Get the auctions by subscriber.
     */
    public function auctions()
    {
        return $this->hasMany('App\Auction')->withDefault();
    }

    /**
     * Get the auctions by subscriber.
     */
    public function subscribers()
    {
        return $this->hasMany('App\Subscriber')->withDefault();
    }
}
