<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Installment extends Model
{
    //
    protected $fillable = ['auction_id', 'subscriber_id', 'due_amount', 'due_date', 'status'];

    public function auction()
    {
        return $this->belongsTo('App\Auction')->withDefault();
    }

    public static function getBySubscriber($subscriber_id)
    {
        $installments = Installment::leftjoin('ledger_entries', 'ledger_entries.installment_id', '=', 'installments.id')
            ->leftjoin('auctions', 'installments.auction_id', '=', 'auctions.id')
            ->where(['installments.subscriber_id' => $subscriber_id, 'installments.status' => 'pending'])
            ->select(
                array (
                    'installments.id',
                    'installments.due_amount',
                    'installments.due_date',
                    'auctions.number'
                )
            )
            ->selectRaw('SUM(ledger_entries.amount) as paid_amount')
            ->groupBy(['installments.id', 'installments.due_amount', 'installments.due_date', 'auctions.number'])
            ->orderBy('installments.due_date', 'DESC')
            ->get()->toArray();

        return $installments;
    }

}



