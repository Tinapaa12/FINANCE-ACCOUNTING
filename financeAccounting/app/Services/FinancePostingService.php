<?php
namespace App\Services;

use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use App\Models\Sales\SalesTransaction;
use Illuminate\Support\Facades\DB;

class FinancePostingService
{
    public static function postSale(SalesTransaction $salesTransaction): JournalEntry
    {
        if ($salesTransaction->is_posted_to_finance) {
            throw new \Exception('Transaction ' . $salesTransaction->order_no . ' has already been posted to Finance.');
        }

        $debitAccount  = self::resolveDebitAccount($salesTransaction->payment_method);
        $creditAccount = self::resolveCreditAccount();

        return DB::transaction(function () use ($salesTransaction, $debitAccount, $creditAccount) {
            $year = now()->format('Y');
            $lastJe = JournalEntry::where('reference_no', 'like', "JE-{$year}-%")
                ->orderBy('journal_entry_id', 'desc')
                ->first();
            $nextNum = $lastJe ? (int) substr($lastJe->reference_no, -3) + 1 : 1;
            $referenceNo = 'JE-' . $year . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            $entry = JournalEntry::create([
                'transaction_date' => now()->format('Y-m-d'),
                'reference_no'     => $referenceNo,
                'description'      => 'Sales transaction - ' . $salesTransaction->order_no,
                'status'           => 'Posted',
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->journal_entry_id,
                'account_id'       => $debitAccount->account_id,
                'description'      => 'Sales transaction - ' . $salesTransaction->order_no,
                'debit'            => $salesTransaction->total_amount,
                'credit'           => 0,
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $entry->journal_entry_id,
                'account_id'       => $creditAccount->account_id,
                'description'      => 'Sales transaction - ' . $salesTransaction->order_no,
                'debit'            => 0,
                'credit'           => $salesTransaction->total_amount,
            ]);

            $salesTransaction->update([
                'is_posted_to_finance' => true,
                'journal_entry_id'     => $entry->journal_entry_id,
            ]);

            return $entry;
        });
    }

    private static function resolveDebitAccount(string $paymentMethod): ChartOfAccount
    {
        $keywords = match ($paymentMethod) {
            'Cash'          => ['Cash on Hand', 'Cash', 'Petty'],
            'Credit Card',
            'Installment'   => ['Receivable', 'AR', 'Accounts Receivable'],
            'Bank Transfer' => ['Bank', 'Cash in Bank'],
            default         => throw new \Exception("Unknown payment method: {$paymentMethod}"),
        };

        foreach ($keywords as $keyword) {
            $account = ChartOfAccount::where('type', 'Asset')
                ->where('account_name', 'like', "%{$keyword}%")
                ->first();
            if ($account) return $account;
        }

        $account = ChartOfAccount::where('type', 'Asset')->first();

        if (!$account) {
            throw new \Exception(
                'No Asset account found. Please create at least one Asset account.'
            );
        }

        return $account;
    }

    private static function resolveCreditAccount(): ChartOfAccount
    {
        $account = ChartOfAccount::where('type', 'Revenue')
            ->where('normal_balance', 'Credit')
            ->first();

        if (!$account) {
            throw new \Exception(
                'No Revenue account found. Please create at least one Revenue account with Credit normal balance.'
            );
        }

        return $account;
    }
}
