<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Scheme;
use DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		
        $schemes = Scheme::leftjoin('ledger_entries', 'ledger_entries.scheme_id', '=', 'schemes.id')
            ->select(array(
                'schemes.id',
                'schemes.title',
                'schemes.start_date',
                'schemes.subscribers',
                'schemes.value',
                'schemes.status',
                DB::raw('sum(if(ledger_entries.ledger_category_id = ' . config('app.chit_ledger_codes.INSTALLMENT') . ', ledger_entries.amount, 0)) as collected_total'),
                DB::raw('sum(if(ledger_entries.ledger_category_id = ' . config('app.chit_ledger_codes.PAYOUT') . ' or ledger_entries.ledger_category_id = ' . config('app.chit_ledger_codes.COMMISSION') . ', ledger_entries.amount, 0)) as expenses_total')
            ))
			->whereRaw('schemes.deleted_at IS NULL')
			->groupBy([
			    'schemes.id',
			    'schemes.title',
                'schemes.start_date',
                'schemes.subscribers',
                'schemes.value',
                'schemes.status',
				'ledger_entries.scheme_id'
				])			
            ->orderBy('schemes.start_date', 'DESC')
            ->offset(0)
            ->limit(10)
            ->get()
            ->toArray();
			
        return view('home', compact('schemes'));
    }
}
