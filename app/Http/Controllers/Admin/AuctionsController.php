<?php

namespace App\Http\Controllers\Admin;


use App\Auction;
use App\Installment;
use App\LedgerEntry;
use App\Member;
use App\Scheme;
use App\SchemePayment;
use App\Subscriber;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSchemesRequest;
use App\Http\Requests\Admin\UpdateSchemesRequest;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use mysql_xdevapi\Exception;

class AuctionsController extends Controller
{

    public function save($scheme_id)
    {

        $auction_dates = Input::get('auction_date');
        $bidAmounts = Input::get('bid_amount');
        $subscribers = Input::get('subscriber');

        foreach ($auction_dates as $idAuction => $eachAuctionDate) {
            $auction = Auction::where(['id' => $idAuction, 'scheme_id' => $scheme_id])->first();

            if ($auction) {
                $auction->auction_date = $eachAuctionDate;
                $auction->bid_amount = $bidAmounts[$idAuction];

                $auction->subscriber_id = ($subscribers[$idAuction]) ? $subscribers[$idAuction] : null;

                $auction->save();
            }
        }

        return redirect()->route('admin.schemes.show', ['id' => $scheme_id]);
    }

    public function generate($scheme_id, $auction_id)
    {
        try {

            DB::beginTransaction();

            $auction = Auction::where(['id' => $auction_id, 'scheme_id' => $scheme_id])->firstOrFail();

            $scheme = $auction->scheme;
            $subscribers = Subscriber::where(['scheme_id' => $scheme_id])->get();
            $installmentData = [];
            foreach ($subscribers as $subscriber) {
                $due_date = date_create($auction->auction_date);
                date_add($due_date, date_interval_create_from_date_string("5 days"));
                $due_date = date_format($due_date, "Y-m-d");

                $installmentData[] = [
                    'auction_id' => $auction->id,
                    'subscriber_id' => $subscriber->id,
                    'due_date' => $due_date,
                    'due_amount' => $scheme->installment,
                    'status' => 'pending'
                ];
            }
            Installment::insert($installmentData);

            $auction->status = 'active';
            $auction->save();

            DB::commit();

            return redirect()->route('admin.schemes.show', ['id' => $scheme->id]);

        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.schemes.show', ['id' => $scheme->id])->with('message', $ex->getMessage());
        }
    }

    public function skip($scheme_id, $auction_id)
    {
        try {
            $auction = Auction::where(['id' => $auction_id, 'scheme_id' => $scheme_id])->firstOrFail();

            $auction->status = 'active';
            $auction->save();
            return redirect()->route('admin.schemes.show', ['id' => $scheme_id]);
        } catch (\Exception $ex) {
            return redirect()->route('admin.schemes.show', ['id' => $scheme_id])->with('message', $ex->getMessage());
        }
    }

    public function awardBid(Request $request, $scheme_id)
    {
        try {

            DB::beginTransaction();
            $auction = Auction::where(['id' => $request->input('auction_id'), 'scheme_id' => $scheme_id, 'status' => 'active'])->firstOrFail();
            $auction->subscriber_id = $request->input('subscriber_id');
            $auction->bid_amount = $request->input('amount');
            $auction->status = 'awarded';
            $auction->save();

            $member = Member::find($auction->subscriber->member_id);

            $description = vsprintf('%s - %d - %s', [
                $auction->scheme->title,
                $auction->number,
                $member->name
            ]);

            $ledgerEntry = LedgerEntry::saveRecord([
				'scheme_id' => $scheme_id,
				'member_id' => $member->id,
                'description' => $description,
                'amount' => $request->input('amount'),
                'entry_date' => date('Y-m-d'),
                'ledger_category_id' => config('app.chit_ledger_codes.PAYOUT')
            ]);

            $pendingAuctions = Auction::where('scheme_id', $scheme_id)
                ->where('status', '!=', 'awarded')
                ->count();

            if ($pendingAuctions <= 0) {
                $scheme = Scheme::find($scheme_id);
                $scheme->status = 'completed';
                $scheme->save();
            }

            DB::commit();

            return redirect()->route('admin.schemes.show', ['id' => $scheme_id]);

        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.schemes.show', ['id' => $scheme_id])->with('message', $ex->getMessage());
        }
        exit;
    }
}
