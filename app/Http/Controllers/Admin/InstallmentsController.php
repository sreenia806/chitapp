<?php

namespace App\Http\Controllers\Admin;


use App\Auction;
use App\Installment;
use App\LedgerEntry;
use App\Member;
use App\Scheme;
use App\Subscriber;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

class InstallmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSchemesRequest $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSchemesRequest $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBySubscriber(Request $request)
    {
        /*
        $scheme = Installment::join(['scheme_id' => $scheme_id])
            ->orderBy('quotes.IdQuote', 'DESC')
            ->paginate(10);
        */
        $installments = Installment::getBySubscriber($request->input('subscriber_id'));

        $html = '<option value="">Please select</option>';

        if ($installments) {
            foreach ($installments as $installment) {
                $still_due = $installment['due_amount'] - $installment['paid_amount'];
                $html .= '<option value="' . $installment['id'] . '" still_due="' . $still_due . '">'
                    . '[' . $installment['number'] . ']'
                    . '-' . $installment['due_date']
                    . '-' . $installment['due_amount']
                    . '-' . $installment['paid_amount'] . '</option>';
            }
        }

        return response()->json(['html' => $html]);
    }


    public function loadData(Request $request)
    {
        $page_size = ($request->input('length')) ? ($request->input('length')) : 20;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;

        // Installment Ticket Subscriber Due Amount	Due Date	Paid Date	Status

        $installments = Installment::leftjoin('subscribers', 'subscribers.id', '=', 'installments.subscriber_id')
            ->join('members', 'members.id', '=', 'subscribers.member_id')
            ->leftjoin('auctions', 'auctions.id', '=', 'installments.auction_id')
            ->select(array(
                'auctions.number',
                'subscribers.ticket',
                DB::raw("CONCAT(members.first_name, ' ', members.last_name) AS name"),
                'installments.due_amount',
                'installments.due_date',
                'installments.paid_date',
                'installments.status'
            ))
            ->where(['auctions.scheme_id' => $request->input('scheme_id')])
            ->orderByRaw('installments.status ASC, auctions.number ASC, subscribers.ticket ASC')
            ->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();

        $totalRows = DB::table('installments')
            ->leftjoin('auctions', 'auctions.id', '=', 'installments.auction_id')
            ->where(['auctions.scheme_id' => $request->input('scheme_id')])->count();

        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $totalRows,
            'data' => $installments
        ]);
    }

	public function getDataByMember(Request $request, $id)
	{
		$page_size = ($request->input('length')) ? ($request->input('length')) : 20;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;

        $installment_query = Installment::query();
		$installment_query = $installment_query->join('subscribers', 'installments.subscriber_id', '=', 'subscribers.id')
			->join('schemes', 'schemes.id', '=', 'subscribers.scheme_id')
			->leftjoin('auctions', 'auctions.id', '=', 'installments.auction_id')
            ->where(['subscribers.member_id' => $id]);

		$totalRows = $installment_query->count();

		$installment_query = $installment_query->leftjoin('ledger_entries', 'installments.id', '=', 'ledger_entries.installment_id')
            ->select(
                array (
                    'schemes.title',
                    'subscribers.ticket',
                    'subscribers.scheme_id',
                    'auctions.number',
					DB::raw("SUM(ledger_entries.amount) AS paid_amount"),
                    'installments.due_amount',
                    'installments.due_date',
					'installments.status',
                    'installments.id',
                    'installments.subscriber_id',
                )
            )
			->groupBy([
			    'installments.id',
                'schemes.title',
                'auctions.number',
                'subscribers.ticket',
                'subscribers.scheme_id',
                'installments.due_amount',
                'installments.due_date',
                'installments.subscriber_id',
                'installments.status']);


        $installments = $installment_query->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();

        foreach ($installments as &$eachInstallment) {
            if ($eachInstallment['status'] == 'pending') {
                $eachInstallment['status'] = '
                    <button class="btn btn-sm btn-info"  data-toggle="modal" data-target="#addMemberPayment">Pay</button>
                ';
            }
        }


        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $totalRows,
            'data' => $installments
        ]);
	}

}
