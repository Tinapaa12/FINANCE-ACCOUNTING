<?php // FinancePostingService — handles automatic journal entry creation when sales transactions are paid. Contains the posting rules per payment method and prevents duplicate posting.
/*
 * ============================================================================
 * INTEGRATION NOTE: Dummy Sales Module
 * ============================================================================
 *
 * This service is designed so that the Finance module does NOT depend on
 * the Sales module directly.  The Sales module (dummy or real) calls this
 * service to post transactions.
 *
 * To swap the dummy Sales module with the real one:
 *
 *   1. The real Sales module's controller (or event listener) calls
 *      `FinancePostingService::postSale($salesTransaction)` the same way
 *      this dummy controller does.
 *
 *   2. The `SalesTransaction` model / `sales_transactions` table is replaced
 *      by the real module's model.  The service only requires:
 *        - order_no / total_amount / payment_method
 *        - is_posted_to_finance (flag) & journal_entry_id (reference)
 *
 *   3. No changes are needed in JournalEntry, JournalEntryLine, or
 *      ChartOfAccount — the Finance module is decoupled.
 *
 * To disable the dummy module, simply remove the route and sidebar link.
 * ============================================================================
 */

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SalesTransaction;
use Illuminate\Support\Facades\DB;

class FinancePostingService
{
    /*
     * Account codes used by the posting rules.
     * These reference the chart_of_accounts seeded by ChartOfAccountsSeeder.
     * If account names/codes change, update them here.
     */
    const ACCOUNT_CASH_ON_HAND      = '1010';
    const ACCOUNT_CASH_IN_BANK      = '1020';
    const ACCOUNT_CREDIT_CARD_REC   = '1100';   // Uses Accounts Receivable as Credit Card Receivable
    const ACCOUNT_SALES_REVENUE     = '4100';

    /**
     * Post a sales transaction to the Finance module.
     * Creates a journal entry and lines, then marks the transaction as posted.
     *
     * @param SalesTransaction $salesTransaction
     * @return JournalEntry
     * @throws \Exception
     */
    public static function postSale(SalesTransaction $salesTransaction): JournalEntry
    {
        // Prevent duplicate posting
        if ($salesTransaction->is_posted_to_finance) {
            throw new \Exception('Transaction ' . $salesTransaction->order_no . ' has already been posted to Finance.');
        }

        // Look up the accounts needed for this transaction
        $debitAccount  = self::resolveDebitAccount($salesTransaction->payment_method);
        $creditAccount = self::resolveAccount(self::ACCOUNT_SALES_REVENUE);

        return DB::transaction(function () use ($salesTransaction, $debitAccount, $creditAccount) {
            // Generate reference: JE-{YYYY}-{NNN} using today's date
            $year = now()->format('Y');
            $lastJe = JournalEntry::where('reference_no', 'like', "JE-{$year}-%")
                ->orderBy('journal_entry_id', 'desc')
                ->first();
            $nextNum = $lastJe ? (int) substr($lastJe->reference_no, -3) + 1 : 1;
            $referenceNo = 'JE-' . $year . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            // Create the journal entry
            $entry = JournalEntry::create([
                'transaction_date' => now()->format('Y-m-d'),
                'reference_no'     => $referenceNo,
                'description'      => 'Sales transaction - ' . $salesTransaction->order_no,
                'status'           => 'Posted',
            ]);

            // Create debit line
            JournalEntryLine::create([
                'journal_entry_id' => $entry->journal_entry_id,
                'account_id'       => $debitAccount->account_id,
                'description'      => 'Sales transaction - ' . $salesTransaction->order_no,
                'debit'            => $salesTransaction->total_amount,
                'credit'           => 0,
            ]);

            // Create credit line
            JournalEntryLine::create([
                'journal_entry_id' => $entry->journal_entry_id,
                'account_id'       => $creditAccount->account_id,
                'description'      => 'Sales transaction - ' . $salesTransaction->order_no,
                'debit'            => 0,
                'credit'           => $salesTransaction->total_amount,
            ]);

            // Mark the sales transaction as posted
            $salesTransaction->update([
                'is_posted_to_finance' => true,
                'journal_entry_id'     => $entry->journal_entry_id,
            ]);

            return $entry;
        });
    }

    /**
     * Resolve the debit account based on the payment method.
     */
    private static function resolveDebitAccount(string $paymentMethod): ChartOfAccount
    {
        $code = match ($paymentMethod) {
            'Cash'         => self::ACCOUNT_CASH_ON_HAND,
            'Credit Card'  => self::ACCOUNT_CREDIT_CARD_REC,
            'Installment'  => self::ACCOUNT_CREDIT_CARD_REC,
            'Bank Transfer'=> self::ACCOUNT_CASH_IN_BANK,
            default        => throw new \Exception("Unknown payment method: {$paymentMethod}"),
        };

        return self::resolveAccount($code);
    }

    /**
     * Find a ChartOfAccount by its account_code.
     *
     * @throws \Exception
     */
    private static function resolveAccount(string $accountCode): ChartOfAccount
    {
        $account = ChartOfAccount::where('account_code', $accountCode)->first();

        if (!$account) {
            throw new \Exception(
                "ChartOfAccount with code '{$accountCode}' not found. " .
                "Run `php artisan db:seed --class=ChartOfAccountsSeeder` to create it."
            );
        }

        return $account;
    }
}
