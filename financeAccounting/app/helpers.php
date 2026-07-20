<?php

use App\Models\AccountPayable\Payment;
use App\Models\GeneralLedger\JournalEntry;

if (!function_exists('audit_log')) {
    function audit_log($loggable, $action, $description = null, $oldValues = null, $newValues = null)
    {
    }
}

if (!function_exists('generate_payment_ref')) {
    function generate_payment_ref(): string
    {
        return generate_seq_ref('PAY');
    }
}

if (!function_exists('generate_expense_ref')) {
    function generate_expense_ref(): string
    {
        return generate_seq_ref('EXP');
    }
}

if (!function_exists('generate_seq_ref')) {
    function generate_seq_ref(string $prefix): string
    {
        $year = now()->format('Y');
        $pattern = "{$prefix}-{$year}-%";

        $lastFromPayment = Payment::where('reference', 'like', $pattern)
            ->orderBy('id', 'desc')
            ->first();

        $lastFromJournal = JournalEntry::where('reference_no', 'like', $pattern)
            ->orderBy('journal_entry_id', 'desc')
            ->first();

        $maxNum = 0;
        if ($lastFromPayment) {
            $num = (int) substr($lastFromPayment->reference, -3);
            $maxNum = max($maxNum, $num);
        }
        if ($lastFromJournal) {
            $num = (int) substr($lastFromJournal->reference_no, -3);
            $maxNum = max($maxNum, $num);
        }

        return $prefix . '-' . $year . '-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    }
}
