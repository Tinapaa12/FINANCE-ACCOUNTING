<?php // JournalEntry model — represents a journal entry with transaction date, reference, description, and status. Has many lines and provides balance check helpers.
namespace App\Models\GeneralLedger;

use App\Models\Sales\SalesTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JournalEntry extends Model
{
    use HasFactory;

    protected $primaryKey = 'journal_entry_id';

    protected $fillable = [
        'transaction_date',
        'reference_no',
        'description',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id', 'journal_entry_id');
    }

    public function salesTransaction(): HasOne
    {
        return $this->hasOne(SalesTransaction::class, 'journal_entry_id', 'journal_entry_id');
    }

    public function totalDebit(): float
    {
        return $this->lines()->sum('debit');
    }

    public function totalCredit(): float
    {
        return $this->lines()->sum('credit');
    }

    public function isBalanced(): bool
    {
        return $this->totalDebit() === $this->totalCredit();
    }
}