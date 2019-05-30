<?php
namespace App\Http\Controllers\Admin;

use App\LedgerEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MonthlyReportsController extends Controller
{
    public function index(Request $r)
    {
        $from    = Carbon::parse(sprintf(
            '%s-%s-01',
            $r->query('y', Carbon::now()->year),
            $r->query('m', Carbon::now()->month)
        ));
        $to      = clone $from;
        $to->day = $to->daysInMonth;

        // TODO : DR
        $exp_q = LedgerEntry::with('ledger_category')
            ->whereBetween('entry_date', [$from, $to]);

        // TODO : CR
        $inc_q = LedgerEntry::with('ledger_category')
            ->whereBetween('entry_date', [$from, $to]);

        $exp_total = $exp_q->sum('amount');
        $inc_total = $inc_q->sum('amount');
        $exp_group = $exp_q->orderBy('amount', 'desc')->get()->groupBy('ledger_category_id');
        $inc_group = $inc_q->orderBy('amount', 'desc')->get()->groupBy('ledger_category_id');
        $profit    = $inc_total - $exp_total;

        $exp_summary = [];
        foreach ($exp_group as $exp) {
            foreach ($exp as $line) {
                if (! isset($exp_summary[$line->ledger_category->name])) {
                    $exp_summary[$line->ledger_category->name] = [
                        'name'   => $line->ledger_category->name,
                        'amount' => 0,
                    ];
                }
                $exp_summary[$line->ledger_category->name]['amount'] += $line->amount;
            }
        }

        $inc_summary = [];
        foreach ($inc_group as $inc) {
            foreach ($inc as $line) {
                if (! isset($inc_summary[$line->ledger_category->name])) {
                    $inc_summary[$line->ledger_category->name] = [
                        'name'   => $line->ledger_category->name,
                        'amount' => 0,
                    ];
                }
                $inc_summary[$line->ledger_category->name]['amount'] += $line->amount;
            }
        }

        return view('admin.monthly_reports.index', compact(
            'exp_summary',
            'inc_summary',
            'exp_total',
            'inc_total',
            'profit'
        ));
    }
}
