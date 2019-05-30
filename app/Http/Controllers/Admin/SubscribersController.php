<?php

namespace App\Http\Controllers\Admin;

use App\Subscriber;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class SubscribersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function update($scheme_id)
    {
        $subscribers = Input::get('subscribers');

        foreach ($subscribers as $idSubscriber => $idMember) {
            $subscriber = Subscriber::where(['id' => $idSubscriber, 'scheme_id' => $scheme_id])->first();
            if ($subscriber) {
                $subscriber->member_id = ($idMember) ? $idMember : null;
                $subscriber->status = ($idMember) ? 'active' : null;
                $subscriber->confirmed_date = ($idMember) ? Carbon::now() : null;
                $subscriber->save();
            }
        }

        return redirect()->route('admin.schemes.show', ['id' => $scheme_id]);
    }


    /**
     * Load Data for Ajax
     */
    public function getByMember(Request $request)
    {
        $subscribers = Subscriber::join('schemes', 'schemes.id', '=', 'subscribers.scheme_id')
            ->where(['subscribers.member_id' => $request->input('member_id'), 'schemes.status' => 'progress'])
            ->select(
                array (
                    'subscribers.id',
                    'schemes.title',
                    'subscribers.ticket'
                )
            )
            ->orderBy('schemes.title')
            ->get()->toArray();

        $html = '<option value="">Please select</option>';

        if ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $html .= '<option value="'.$subscriber['id'].'">[' . $subscriber['ticket'] . ']-' . $subscriber['title'] . '</option>';
            }
        }

        return response()->json(['html' => $html]);
    }
	
	
	public function getDataByMember(Request $request, $id)
	{
        $page_size = ($request->input('length')) ? ($request->input('length')) : 20;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;

        $subscriber_query = Subscriber::query();
		$subscriber_query = $subscriber_query->join('schemes', 'schemes.id', '=', 'subscribers.scheme_id')
			->leftjoin('auctions', function($join) {
				   $join->on('schemes.id', '=', 'auctions.scheme_id')
				   ->on('subscribers.id', '=', 'auctions.subscriber_id')
				   ->on('auctions.status', '=', DB::Raw('\'awarded\''));
				 })
            ->where(['subscribers.member_id' => $id])
            ->select(
                array (
					'schemes.title',
                    'subscribers.ticket',
                    'schemes.installment',
					'auctions.bid_amount',
					'schemes.start_date',
					'schemes.status',
                )
            );

        $totalRows = $subscriber_query->count();

        $subscribers = $subscriber_query->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();


        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $totalRows,
            'data' => $subscribers
        ]);
	}
}
