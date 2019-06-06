<?php

namespace App\Http\Controllers\Admin;

use App\Installment;
use App\LedgerEntry;
use App\LedgerCategory;
use App\Member;
use App\Scheme;
use App\Subscriber;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\StoreLedgerEntriesRequest;
use App\Http\Requests\Admin\UpdateLedgerEntriesRequest;
use App\Http\Requests\Admin\AddPaymentsRequest;
// use Illuminate\Support\Facades\Session;
use DB;
use Illuminate\Support\Facades\Input;

class LedgerEntriesController extends Controller
{
    /**
     * Display a listing of Ledger Entry.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('ledger_entry_access')) {
            return abort(401);
        }
        // if ($filterBy = Input::get('filter')) {
        //     if ($filterBy == 'all') {
        //         Session::put('LedgerEntry.filter', 'all');
        //     } elseif ($filterBy == 'my') {
        //         Session::put('LedgerEntry.filter', 'my');
        //     }
        // }


        return view('admin.ledger_entries.index');
    }

    /**
     * Show the form for creating new Expense.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('ledger_entry_create')) {
            return abort(401);
        }

        $ledger_categories = \App\LedgerCategory::whereNotIn('id', [
                config('app.chit_ledger_codes.INSTALLMENT'),
                config('app.chit_ledger_codes.PAYOUT'),
                config('app.chit_ledger_codes.COMMISSION')
            ])
            ->get()->pluck('name', 'id')
            ->prepend(trans('quickadmin.qa_please_select'), '');

        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        return view('admin.ledger_entries.create', compact('ledger_categories', 'created_bies'));
    }

    /**
     * Store a newly created Expense in storage.
     *
     * @param  \App\Http\Requests\StoreExpensesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLedgerEntriesRequest $request)
    {
        if (! Gate::allows('ledger_entry_create')) {
            return abort(401);
        }

        try {

            DB::beginTransaction();
            LedgerEntry::saveRecord($request->all());
            DB::commit();

            return redirect()->route('admin.ledger_entries.index');

        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.ledger_entries.create')->with('message', $ex->getMessage());
        }
    }


    /**
     * Show the form for editing Expense.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('ledger_entry_edit')) {
            return abort(401);
        }

        $ledger_categories = \App\LedgerCategory::whereNotIn('id', [
            config('app.chit_ledger_codes.INSTALLMENT'),
            config('app.chit_ledger_codes.PAYOUT'),
            config('app.chit_ledger_codes.COMMISSION')
        ])
            ->get()
            ->pluck('name', 'id')
            ->prepend(trans('quickadmin.qa_please_select'), '');
        $created_bies = \App\User::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        $ledger_entry = LedgerEntry::findOrFail($id);

        return view('admin.ledger_entries.edit', compact('ledger_entry', 'ledger_categories', 'created_bies'));
    }

    /**
     * Update Expense in storage.
     *
     * @param  \App\Http\Requests\UpdateExpensesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLedgerEntriesRequest $request, $id)
    {
        if (! Gate::allows('ledger_entry_edit')) {
            return abort(401);
        }
        try {

            DB::beginTransaction();

            $ledger_entry = LedgerEntry::findOrFail($id);

            $new_category = LedgerCategory::findOrFail($request->input('ledger_category_id'));


            if ($ledger_entry->ledger_category->ledger_type == $new_category->ledger_type) {
                $difference = $request->input('amount') - $ledger_entry->amount;
                $difference = ($new_category->ledger_type == 'CR') ? $difference : -($difference);
            } else {
                $difference = $request->input('amount') + $ledger_entry->amount;
                $difference = ($new_category->ledger_type == 'CR') ? $difference : -($difference);
            }

            $balance = $ledger_entry->balance + $difference;

            $ledger_entry->update($request->all() + ['balance' => $balance]);

            DB::table('ledger_entries')
                      ->whereRaw('id > ' . $id)
                      ->increment('balance', $difference);

            DB::commit();
            return redirect()->route('admin.ledger_entries.index');
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.ledger_entries.edit', ['id' => $id])->with('message', $ex->getMessage());
        }
    }


    /**
     * Display Expense.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('ledger_entry_view')) {
            return abort(401);
        }
        $ledger_entry = LedgerEntry::findOrFail($id);

        return view('admin.ledger_entries.show', compact('ledger_entry'));
    }



    public function addPayment(addPaymentsRequest $request)
    {
        if (! Gate::allows('payment_create')) {
            return abort(401);
        }

        try {

            DB::beginTransaction();

            $subscriber = Subscriber::find($request->input('subscriber_id'));
			$member = $subscriber->member;
            $description = 'Installment - ' . $member->name;
            $amount = $request->input('amount') * 1;//make sure it is double

            $extra_paid = 0;

			$installment = Installment::find($request->input('installment_id'));
			$scheme = Scheme::find($installment->auction->scheme_id);

			$payments = LedgerEntry::where(['installment_id' => $request->input('installment_id')])
				->selectRaw('SUM(ledger_entries.amount) as paid_amount')
				->groupBy(['installment_id'])
				->first();

			$balance = $installment->due_amount - $payments['paid_amount'];

			if ($amount > $balance) {
				throw new \Exception('Pay only pending amount', 111);
			} elseif ($amount == $balance) {
				$installment->status = 'paid';
				$installment->paid_date = date('Y-m-d');
				$installment->update();
			}

			$description .= ':' . $scheme->title . ' - [' . $subscriber->ticket . '] - [' . $installment->auction->number . ']';


            $ledgerEntry = LedgerEntry::saveRecord([
				'member_id' => $member->id,
				'scheme_id' => $subscriber->scheme_id,
				'installment_id' => $installment->id,
                'description' => $description,
                'amount' => $amount,
                'entry_date' => date('Y-m-d'),
                'ledger_category_id' => config('app.chit_ledger_codes.INSTALLMENT')
            ]);

            DB::commit();

			return response()->json(['status' => 1]);

        } catch (\Exception $ex) {
            DB::rollback();

			return response()->json(['status' => 0, 'error' => $ex->getMessage()]);
        }
    }



	public function addSchemeExpense(Request $request)
	{

        try {

            DB::beginTransaction();
            $ledger_category = $request->input('category', 3);
            $ledgerEntry = LedgerEntry::saveRecord([
                'description' => $request->input('description'),
                'amount' => $request->input('amount'),
				'scheme_id' => $request->input('scheme_id'),
                'entry_date' => date('Y-m-d'),
                'ledger_category_id' => $ledger_category
            ]);

            DB::commit();

			return response()->json(['status' => 1]);
        } catch (\Exception $ex) {
            DB::rollback();
			return response()->json(['status' => 0, 'error' => $ex->getMessage()]);
        }

	}


    /**
     * Remove Expense from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('ledger_entry_delete')) {
            return abort(401);
        }

        try {

            DB::beginTransaction();
            $ledger_entry = LedgerEntry::findOrFail($id);
            $difference = ($ledger_entry->ledger_category->ledger_type == 'CR') ? -($ledger_entry->amount) : $ledger_entry->amount;

            $ledger_entry->delete();

            DB::table('ledger_entries')
                      ->whereRaw('id > ' . $id)
                      ->increment('balance', $difference);

			if ($ledger_entry->ledger_category_id == config('app.chit_ledger_codes.INSTALLMENT')) {
				DB::table('installments')
					->where('id', $ledger_entry->installment_id)
					->update(['status' => 'pending']);
			}

			if ($ledger_entry->ledger_category_id == config('app.chit_ledger_codes.PAYOUT')) {
				//DB::table('auction')
				//	->where('id', $ledger_entry->installment_id)
				//	->update(['status' => 'pending']);
                throw new Exception('1', 'not allowed');
                //config('app.chit_ledger_codes.PAYOUT'),
			}

            DB::commit();
            return redirect()->route('admin.ledger_entries.index');
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.ledger_entries.index')->with('message', $ex->getMessage());
        }
    }

	public function loadData(Request $request)
	{
		$page_size = ($request->input('length')) ? ($request->input('length')) : 20;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;

		$ledger_query = LedgerEntry::query();
		$ledger_query = $ledger_query->leftjoin('ledger_categories', 'ledger_entries.ledger_category_id', '=', 'ledger_categories.id');
		$ledger_query = $ledger_query->select(array(
				'ledger_entries.id',
				'ledger_categories.name',
				'ledger_entries.description',
				'ledger_entries.entry_date',
                DB::raw("IF(ledger_categories.ledger_type='CR', ledger_entries.amount, '') AS payment"),
                DB::raw("IF(ledger_categories.ledger_type='DR', ledger_entries.amount, '') AS expense"),
				'ledger_entries.balance',
				'ledger_entries.scheme_id',
				'ledger_entries.ledger_category_id'
            ));

        $totalRows = $ledger_query->count();
		// date filters
		if ($request->input('start_date')) {
			$ledger_query = $ledger_query->where('entry_date', '>=', $request->input('start_date') . ' 00:00:00');
		}

		if ($request->input('end_date')) {
			$ledger_query = $ledger_query->where('entry_date', '<=', $request->input('end_date') . ' 23:59:59');
		}

        $filteredRows = $ledger_query->count();

        $ledger_query = $ledger_query->orderBy('ledger_entries.id');
        $ledgers = $ledger_query->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();

		// data massage
		foreach ($ledgers as &$ledger) {
			$ledger['payment'] = ($ledger['payment']) ? '₹ ' . number_format($ledger['payment'], 2) : '';
			$ledger['expense'] = ($ledger['expense']) ? '₹ ' . number_format($ledger['expense'], 2) : '';
			$ledger['balance'] = '₹ ' . number_format($ledger['balance'], 2);

			$ledger['actions'] = '';

			if ($request->input('gate_allow_edit') == '1' && empty($ledger['scheme_id'])) {
			    // $ledger['actions'] .= '<a href="' . route('admin.ledger_entries.show', [$ledger['id']]) . '" class="btn btn-xs btn-primary">' . trans('quickadmin.qa_view') . '</a>';
			    $ledger['actions'] .= '<a href="' . route('admin.ledger_entries.edit', [$ledger['id']]) . '" class="btn btn-xs btn-info">' . trans('quickadmin.qa_edit') . '</a>';
			}

			if ($request->input('gate_allow_delete') == '1' && $ledger['ledger_category_id'] != config('app.chit_ledger_codes.PAYOUT')) {
			    $ledger['actions'] .= '<a href="' . route('admin.ledgerdelete', [$ledger['id']]) . '" class="btn btn-xs btn-danger">Delete</a>';
			    /*
                $ledger['actions'] .= '
				 <form style="display: inline-block;" method="POST" onSubmit="return confirm(\'Are you sure want to delete?\');"
				 action="' . route('admin.ledger_entries.destroy', [$ledger['id']]) . '" >
				    <input type="hidden" name="_method" value="DELETE" />
					<input class="btn btn-xs btn-danger" type="submit" value="Delete" >
				</form>
				';
			    */
			}
		}

        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $filteredRows,
            'data' => $ledgers
        ]);
	}

	public function ledgerByScheme(Request $request, $id)
	{
		$page_size = ($request->input('length')) ? ($request->input('length')) : 10;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;

        // Installment Ticket Subscriber Due Amount	Due Date	Paid Date	Status

		$ledger_query = LedgerEntry::query();
		$ledger_query = $ledger_query->leftjoin('ledger_categories', 'ledger_entries.ledger_category_id', '=', 'ledger_categories.id');
		$ledger_query = $ledger_query->select(array(
				'ledger_entries.entry_date',
                'ledger_entries.description',
                DB::raw("IF(ledger_categories.ledger_type='CR', ledger_entries.amount, '') AS payment"),
                DB::raw("IF(ledger_categories.ledger_type='DR', ledger_entries.amount, '') AS expense")
            ));
		$ledger_query = $ledger_query->where(['scheme_id' => $id]);

        $totalRows = $ledger_query->count();

		// More Filters


        $ledger_entries = $ledger_query->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();


        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $totalRows,
            'data' => $ledger_entries
        ]);
	}


	public function getDataByMember(Request $request, $id)
	{
		$page_size = ($request->input('length')) ? ($request->input('length')) : 20;
        $start = ($request->input('start')) ? ($request->input('start')) : 0;

        $ledger_query = LedgerEntry::query();
		$ledger_query = $ledger_query
            ->select(
                array (
                    'description',
                    'amount',
					'entry_date',
                )
            );
		$ledger_query = $ledger_query->where(['member_id' => $id])
			->where(['ledger_category_id' => config('app.chit_ledger_codes.INSTALLMENT')]);

        $totalRows = $ledger_query->count();

        $ledgers = $ledger_query->offset($start)
            ->limit($page_size)
            ->get()
            ->toArray();


        return response()->json([
            'draw' =>  intval($request->input('draw')),
            'recordsTotal' => $totalRows,
            'recordsFiltered' => $totalRows,
            'data' => $ledgers
        ]);
	}

    /**
     * Delete all selected Expense at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

    }

}
