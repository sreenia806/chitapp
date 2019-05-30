<?php

namespace App\Http\Controllers\Admin;


use App\Auction;
use App\Installment;
use App\LedgerEntry;
use App\Member;
use App\Scheme;
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

class SchemesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('scheme_access')) {
            return abort(401);
        }

        return view('admin.schemes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if (! Gate::allows('scheme_create')) {
            return abort(401);
        }

        return view('admin.schemes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSchemesRequest $request)
    {
        //

        if (! Gate::allows('scheme_create')) {
            return abort(401);
        }

        try {

            DB::beginTransaction();

            $scheme = Scheme::create($request->all());
            // start_date is non_fillable
            if (!empty($request->input('start_date'))) {
                $scheme->start_date = $request->input('start_date');
                $scheme->save();
            }

            $subscriberData = [];
            // create empty subscribers( members)
            for ($i = 0; $i < $scheme->subscribers; $i++) {
                $subscriberData[] = [
                    'ticket' => $i + 1,
                    'scheme_id' => $scheme->id,
                ];
            }
            Subscriber::insert($subscriberData);

            // auctions
            $auctionData = [];
            // create empty auction, the admin fill the projected
            for ($i = 0; $i < $scheme->subscribers; $i++) {
                $auctionDate = null;
                if (!empty($scheme->start_date)) {
                    $date = date_create($scheme->start_date);
                    date_add($date, date_interval_create_from_date_string("{$i} months"));
                    $auctionDate = date_format($date, "Y-m-d");
                }

                $auctionData[] = [
                    'number' => $i + 1,
                    'scheme_id' => $scheme->id,
                    'auction_date' => $auctionDate,
                    'status' => 'projected'
                ];
            }

            Auction::insert($auctionData);

            DB::commit();

            return redirect()->route('admin.schemes.index');
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.schemes.create')->with('message', $ex->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //        //
		if (! Gate::allows('scheme_view')) {
            return abort(401);
        }


		if ($request->input('tab')) {
			$request->session()->put('scheme_tab', $request->input('tab'));
		} elseif (!$request->session()->get('scheme_tab')) {
			$request->session()->put('scheme_tab', 'auctions');
		}

		// $incomes = \App\Income::where('created_by_id', $id)->get();
		// $expenses = \App\Expense::where('created_by_id', $id)->get();
		$chitschemes = [];

		$members = Member::orderBy('last_name')
            ->get(['id','first_name','last_name'])
            ->pluck('name','id');
		//$members = $members->toArray();
        $members->prepend("please select", "");

        $scheme = Scheme::findOrFail($id);
        $auctions = Auction::where('scheme_id', $id)->get();

		// ledger entries by auction
		$arrAuctionCollected = LedgerEntry::getBySchemeAuctions($id);


        $subscribers = Subscriber::where('scheme_id', $id)->get();
        $select_subscribers = Subscriber::getSubscribers($id);// where('scheme_id', $id)->get();

		$awarded_subscribers = [];
		foreach ($auctions as $each_auction) {
			if ($each_auction->status == 'awarded') {
				$awarded_subscribers[] = $each_auction->subscriber_id;
			}
		}

		$remaining_subscribers[] = "please select";
		foreach($select_subscribers as $each_id => $each_subscriber) {
			if (!in_array($each_id, $awarded_subscribers)) {
				$remaining_subscribers[$each_id] = $each_subscriber;
			}
		}


        $installments = [];//Installment::where('scheme_id', $id)->get();

        if ($scheme->status == 'progress') {
            foreach ($auctions as &$auction) {
                if ($auction['status'] == 'projected' || $auction['status'] == 'active') {
                    $auction['showbutton'] = true;
                    break;
                }
            }
        }

		$ledger_query = LedgerEntry::query();
		$ledger_query = $ledger_query->leftjoin('ledger_categories', 'ledger_entries.ledger_category_id', '=', 'ledger_categories.id');
		$ledger_query = $ledger_query->select(array(
                DB::raw("SUM(IF(ledger_categories.ledger_type='CR', ledger_entries.amount, 0)) AS payment"),
                DB::raw("SUM(IF(ledger_categories.ledger_type='DR', ledger_entries.amount, 0)) AS expense")
            ));
        $ledger_query = $ledger_query->groupBy(['ledger_entries.scheme_id']);
		$ledger_query = $ledger_query->where(['scheme_id' => $id]);
		$scheme_details = $ledger_query->get()->toArray();
		if (!$scheme_details) {
			$scheme_details[] = [
				'payment' => 0,
				'expense' => 0
			];
		}

		$select_subscribers->prepend("please select", "");

        return view('admin.schemes.show', compact('scheme', 'scheme_details', 'auctions',
			'arrAuctionCollected', 'installments', 'subscribers', 'members',
			'select_subscribers', 'remaining_subscribers')); //, 'expense_categories', 'income_categories', 'currencies', 'incomes', 'expenses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if (! Gate::allows('scheme_edit')) {
            return abort(401);
        }

        $scheme = Scheme::findOrFail($id);
        $scheme->installment = number_format($scheme->installment, 2);
        $scheme->value = number_format($scheme->value, 2);

        return view('admin.schemes.edit', compact('scheme'));
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
        //
        if (! Gate::allows('scheme_edit')) {
            return abort(401);
        }

        try {

            DB::beginTransaction();

            $scheme = Scheme::findOrFail($id);
            // read existing values in DB
            $oldduration = $scheme->duration;
            $oldsubscribers = $scheme->subscribers;
            $oldvalue = $scheme->value;

            // start_date is non_fillable
            $scheme->start_date = $request->input('start_date');

            $scheme->update($request->all());

            // reset subscribers
            if ($oldsubscribers < $scheme->subscribers) {
                $subscriberData = [];
                // create empty subscribers( members)
                for ($i = $oldsubscribers; $i < $scheme->subscribers; $i++) {
                    $subscriberData[] = [
                        'ticket' => $i + 1,
                        'scheme_id' => $scheme->id
                    ];
                }
                Subscriber::insert($subscriberData);
            } else {
                for ($i = $scheme->subscribers; $i < $oldsubscribers; $i++) {
                    $subscriber = Subscriber::where(['scheme_id' => $scheme->id, 'ticket' => ($i + 1)]);
                    if ($subscriber) {
                        $subscriber->delete();
                    }
                }
            }

            // reset auctions
            if ($oldsubscribers < $scheme->subscribers) {
                $auctionData = [];
                // create empty auction, the admin fill the projected
                for ($i = $oldsubscribers; $i < $scheme->subscribers; $i++) {
                       $auctionData[] = [
                        'number' => $i + 1,
                        'scheme_id' => $scheme->id
                    ];
                }

                Auction::insert($auctionData);

            } else {

                for ($i = $scheme->subscribers; $i < $oldsubscribers; $i++) {
                    $auction = Auction::where(['scheme_id' => $scheme->id, 'number' => ($i + 1)]);
                    if ($auction) {
                        $auction->delete();
                    }
                }
            }

            // set auction date
            if (!empty($scheme->start_date)) {
                for ($i = 0; $i < $scheme->subscribers; $i++) {
                    $date = date_create($scheme->start_date);
                    date_add($date, date_interval_create_from_date_string("{$i} months"));
                    $auctionDate = date_format($date, "Y-m-d");

                    $auction = Auction::where(['scheme_id' => $scheme->id, 'number' => ($i + 1)])->first();
                    if ($auction && empty($auction->auction_date)) {
                        $auction->auction_date = $auctionDate;
                        $auction->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.schemes.index');
        } catch (\Exception $ex) {

            DB::rollback();
            return redirect()->route('admin.schemes.index')->with('message', $ex->getMessage());
        }
    }

    /*
     * STart the scheme
     *
     * @param int $id
     */
    public function start($id)
    {
        $scheme = Scheme::findOrFail($id);

        if ($scheme->status == 'pending') {
            $scheme->status = 'progress';
            $scheme->save();


        } else {
            return redirect()->route('admin.schemes.index')->with('message', 'Scheme already started, now in ' . $scheme->status);
        }

        return redirect()->route('admin.schemes.show', ['id' => $id]);
    }

    public function loadData(Request $request)
    {

        $page_size = ($request->input('length')) ? ($request->input('length')) : 20;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;

        $scheme_query = Scheme::query();

        $scheme_query = $scheme_query->whereRaw('deleted_at IS NULL');

        $totalRows = $scheme_query->count();

        // More Filters
        $search = $request->input('search');
        if (isset($search['value'])) {
            $scheme_query = $scheme_query->where('title', $search['value'])
                ->orWhere('title', 'like', '%' . $search['value'] . '%');
        }
        $filteredRows = $scheme_query->count();

        // Order By
        $order = $request->input('order');
        if (isset($order[0]['column'])) {
            $columns = $request->input('columns');
            $sortColumn = $columns[$order[0]['column']]['data'];
            if ($sortColumn) {
                $scheme_query = $scheme_query->orderBy($sortColumn, $order[0]['dir']);
            }
        }

        $schemes = $scheme_query->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();

        foreach ($schemes as &$scheme) {
            $scheme['actions'] = '';
            if ($request->input('gate_allow_view', '') == 1) {
                $scheme['actions'] .= '<a href="' . route('admin.schemes.show', [$scheme['id']]) . '" class="btn btn-xs btn-primary">' . trans('quickadmin.qa_view') . '</a>';
            }

            if ($request->input('gate_allow_edit', '') == 1 && $scheme['status'] == 'pending') {
                $scheme['actions'] .= ' <a href="' . route('admin.schemes.edit', [$scheme['id']]) . '" class="btn btn-xs btn-info">' . trans('quickadmin.qa_edit') . '</a>';
            }
			
			$scheme['duration'] = $scheme['duration'] . '/' . $scheme['subscribers'];
			$scheme['value'] = '₹ ' . number_format($scheme['value'], 2);
        }

        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $filteredRows,
            'data' => $schemes
        ]);

    }

    public function loadLedger(Request $request)
    {
        $page_size = ($request->input('length')) ? ($request->input('length')) : 20;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;
        $payments = [];

        // member name, chit scheme title, ticket/subscription, installment, paid amount, paid date

        $payments = SchemePayment::leftjoin('members', 'members.id', '=', 'scheme_payments.member_id')
            ->leftjoin('installments', 'installments.id', '=', 'scheme_payments.installment_id')
            ->leftjoin('subscribers', 'subscribers.id', '=', 'installments.subscriber_id')
            ->leftjoin('auctions', 'auctions.id', '=', 'installments.auction_id')
            ->leftjoin('schemes', 'auctions.scheme_id', '=', 'schemes.id')
            ->leftjoin('ledger_entries', 'ledger_entries.id', '=', 'scheme_payments.ledger_entry_id')
            ->select(array(
                DB::raw("CONCAT(members.first_name, ' ', members.last_name) AS name"),
                'schemes.title',
                'subscribers.ticket',
                'auctions.number',
                'ledger_entries.amount',
                'ledger_entries.entry_date'
            ))
            ->orderBy('scheme_payments.id', 'DESC')
            ->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();

        $totalRows = DB::table('scheme_payments')->count();

        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $totalRows,
            'data' => $payments
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        if (! Gate::allows('scheme_delete')) {
            return abort(401);
        }
        $scheme = Scheme::findOrFail($id);
        if ($scheme->status == 'pending') {
            $scheme->delete();
        } else {
            return redirect()->route('admin.schemes.index')->with('message', 'Now Allowed to delete the running or completed Scheme');
        }

        return redirect()->route('admin.schemes.index');
    }
}
