<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class Expense
 *
 * @package App
 * @property string $expense_category
 * @property string $entry_date
 * @property string $amount
 * @property string $created_by
*/
class LedgerEntry extends Model
{
    protected $fillable = ['entry_date', 'description', 'amount', 'balance', 'scheme_id', 'currency_id', 'ledger_category_id', 'created_by_id', 'member_id', 'installment_id'];

    public static function saveRecord($data = [])
    {
        $last_entry = LedgerEntry::orderBy('id', 'desc')->first();
        if ($last_entry) {
            $balance = $last_entry->balance;
        } else {
            $balance = 0;
        }
        $ledger_category = LedgerCategory::findOrFail($data['ledger_category_id']);

        if ($ledger_category->ledger_type == 'CR') {
            $balance = $balance + $data['amount'];
        } else {
            $balance = $balance - $data['amount'];
        }
		
		$additional_data = ['balance' => $balance, 'currency_id' => Auth::user()->currency_id];
		if (!isset($data['scheme_id'])) {
			$additional_data['scheme_id'] = null;
		}
		if (!isset($data['member_id'])) {
			$additional_data['member_id'] = null;
		}
		if (!isset($data['installment_id'])) {
			$additional_data['installment_id'] = null;
		}

        $ledger_entry = LedgerEntry::create($data + $additional_data);
        return $ledger_entry;
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setLedgerCategoryIdAttribute($input)
    {
        $this->attributes['ledger_category_id'] = $input ? $input : null;
    }

    /**
     * Set attribute to date format
     * @param $input
     */
    public function setEntryDateAttribute($input)
    {
        if ($input != null && $input != '') {
            $this->attributes['entry_date'] = Carbon::createFromFormat(config('app.date_format'), $input)->format('Y-m-d');
        } else {
            $this->attributes['entry_date'] = null;
        }
    }

    /**
     * Get attribute from date format
     * @param $input
     *
     * @return string
     */
    public function getEntryDateAttribute($input)
    {
        $zeroDate = str_replace(['Y', 'm', 'd'], ['0000', '00', '00'], config('app.date_format'));

        if ($input != $zeroDate && $input != null) {
            return Carbon::createFromFormat('Y-m-d', $input)->format(config('app.date_format'));
        } else {
            return '';
        }
    }

    /**
     * Set to null if empty
     * @param $input
     */
    public function setCreatedByIdAttribute($input)
    {
        $this->attributes['created_by_id'] = $input ? $input : null;
    }

    public function ledger_category()
    {
        return $this->belongsTo(LedgerCategory::class, 'ledger_category_id');
    }

    public function ledger_currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
	
	
    public static function getBySchemeAuctions($scheme_id)
    {
		
		$ledger_query = self::query();
		$ledger_query = $ledger_query->leftjoin('installments', 'ledger_entries.installment_id', '=', 'installments.id');
		$ledger_query = $ledger_query->select(array(
				'installments.auction_id',   
            ))
			->selectRaw('SUM(ledger_entries.amount) as paid_amount');
		$ledger_query = $ledger_query->where(['scheme_id' => $scheme_id])
						->where(['ledger_category_id' => config('app.chit_ledger_codes.INSTALLMENT')])
						->groupBy(['installments.auction_id']);
		
		
        $ledger_entries = $ledger_query->get()->toArray();
		$auction_collected = [];
		foreach($ledger_entries as $each_ledgerentry) {
			$auction_collected[$each_ledgerentry['auction_id']] = $each_ledgerentry['paid_amount'];
		}

		//
        return $auction_collected;
	}

}
