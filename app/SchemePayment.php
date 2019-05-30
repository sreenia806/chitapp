<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\FilterByUser;

/**
 * Class Expense
 *
 * @package App
 * @property string $payment
 * @property string $entry_date
 * @property string $amount
 * @property string $created_by
*/
class SchemePayment extends Model
{
    //use FilterByUser;

    protected $fillable = ['member_id', 'installment_id', 'ledger_entry_id'];


    public function ledger_entry()
    {
        return $this->belongsTo(LedgerEntry::class, 'ledger_entry_id');
    }
}
