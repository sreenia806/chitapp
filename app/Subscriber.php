<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Subscriber extends Model
{
    //
    /**
     * Get the member by subscriber.
     */
    public function member()
    {
        return $this->belongsTo('App\Member')->withDefault();
    }
	
	public static function getSubscribers($id)
	{
		$subscribers = self::where('subscribers.scheme_id', '=', $id)
            ->join('members', function ($join) {
                $join->on('members.id', '=', 'subscribers.member_id');
            })
            ->where('subscribers.status', '=', 'active')
            ->get([
                'subscribers.id',
                'subscribers.ticket',
                'members.first_name',
                'members.last_name'
            ])
            ->map(function ($subscriber) {
                return ['id' => $subscriber->id, 'name' => '[' . $subscriber->ticket . ']' . $subscriber->first_name . ' ' . $subscriber->last_name];
            })->pluck('name', 'id');

        return $subscribers;
	}
}
