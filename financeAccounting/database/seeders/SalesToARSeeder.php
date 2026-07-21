<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use App\Models\Invoice;
use App\Models\Sales\SalesTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalesToARSeeder extends Seeder
{
    public function run(): void
    {
        $coa = ChartOfAccount::pluck('account_id', 'account_code');

        $cash     = $coa['1010'] ?? null;
        $ar       = $coa['1100'] ?? null;
        $revenue  = $coa['4100'] ?? null;
        $vatOutput = $coa['2300'] ?? null;

        if (!$cash || !$ar || !$revenue || !$vatOutput) {
            return;
        }

        $customers = [
            ['name' => 'Santos Enterprises',     'email' => 'santos@example.com',     'phone' => '0917-555-0101', 'address' => '123 Rizal Ave, Manila'],
            ['name' => 'ABC Trading Co.',         'email' => 'abc@example.com',        'phone' => '0918-555-0202', 'address' => '456 Gov. Drive, Quezon City'],
            ['name' => 'Cruz & Sons Hardware',    'email' => 'cruz@example.com',       'phone' => '0920-555-0303', 'address' => '789 National Highway, Cebu'],
            ['name' => 'Reyes Medical Supply',    'email' => 'reyes@example.com',      'phone' => '0922-555-0404', 'address' => '321 Doctors Ave, Davao'],
            ['name' => 'Lim Grocery Trading',     'email' => 'lim@example.com',        'phone' => '0925-555-0505', 'address' => '654 Public Market, Bacolod'],
            ['name' => 'Garcia Auto Parts',       'email' => 'garcia@example.com',     'phone' => '0927-555-0606', 'address' => '987 Highway South, Laguna'],
        ];

        $customerMap = [];
        foreach ($customers as $data) {
            $c = Customer::firstOrCreate(['name' => $data['name']], $data);
            $customerMap[$c->name] = $c->id;
        }

        // === 1: Santos Enterprises — Wholesale Electronics (Paid - Cash) ===

        $st1 = SalesTransaction::create([
            'order_no'             => 'SO-2026-009',
            'customer_name'        => 'Santos Enterprises',
            'total_amount'         => 185000,
            'payment_method'       => 'Cash',
            'status'               => 'Paid',
            'is_posted_to_finance' => true,
        ]);

        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-021'],
            [
                'customer_id'  => $customerMap['Santos Enterprises'],
                'type'         => 'invoice',
                'invoice_date' => '2026-07-05',
                'due_date'     => '2026-08-04',
                'currency'     => 'PHP',
                'subtotal'     => 165178.57,
                'vat_amount'   => 19821.43,
                'total'        => 185000,
                'status'       => 'paid',
                'notes'        => 'Wholesale electronics - 10 units power inverters',
            ]
        );

        $je1 = JournalEntry::create([
            'transaction_date' => '2026-07-05',
            'reference_no'     => 'JE-2026-013',
            'description'      => 'Sales revenue - Santos Enterprises (wholesale electronics)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $cash, 'description' => 'Cash sales - Santos Enterprises', 'debit' => 185000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $revenue, 'description' => 'Sales revenue', 'debit' => 0, 'credit' => 165178.57]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $vatOutput, 'description' => 'Output VAT 12%', 'debit' => 0, 'credit' => 19821.43]);

        // === 2: Cruz & Sons Hardware — Construction Supplies (Pending - unpaid) ===

        $st2 = SalesTransaction::create([
            'order_no'             => 'SO-2026-010',
            'customer_name'        => 'Cruz & Sons Hardware',
            'total_amount'         => 320000,
            'payment_method'       => 'Cash',
            'status'               => 'Pending',
            'is_posted_to_finance' => false,
        ]);

        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-022'],
            [
                'customer_id'  => $customerMap['Cruz & Sons Hardware'],
                'type'         => 'invoice',
                'invoice_date' => '2026-07-10',
                'due_date'     => '2026-08-09',
                'currency'     => 'PHP',
                'subtotal'     => 285714.29,
                'vat_amount'   => 34285.71,
                'total'        => 320000,
                'status'       => 'sent',
                'notes'        => 'Construction supplies - steel bars and cement',
            ]
        );

        // === 3: Reyes Medical Supply — Medical Equipment (Paid - Cash) ===

        $st3 = SalesTransaction::create([
            'order_no'             => 'SO-2026-011',
            'customer_name'        => 'Reyes Medical Supply',
            'total_amount'         => 567500,
            'payment_method'       => 'Cash',
            'status'               => 'Paid',
            'is_posted_to_finance' => true,
        ]);

        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-023'],
            [
                'customer_id'  => $customerMap['Reyes Medical Supply'],
                'type'         => 'invoice',
                'invoice_date' => '2026-07-12',
                'due_date'     => '2026-08-11',
                'currency'     => 'PHP',
                'subtotal'     => 506696.43,
                'vat_amount'   => 60803.57,
                'total'        => 567500,
                'status'       => 'paid',
                'notes'        => 'Medical equipment - 5 hospital beds and monitors',
            ]
        );

        $je3 = JournalEntry::create([
            'transaction_date' => '2026-07-12',
            'reference_no'     => 'JE-2026-014',
            'description'      => 'Sales revenue - Reyes Medical Supply',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $cash, 'description' => 'Cash sales - Reyes Medical Supply', 'debit' => 567500, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $revenue, 'description' => 'Sales revenue', 'debit' => 0, 'credit' => 506696.43]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $vatOutput, 'description' => 'Output VAT 12%', 'debit' => 0, 'credit' => 60803.57]);

        // === 4: Lim Grocery Trading — Grocery Wholesale (Pending - Installment with down payment) ===

        $st4 = SalesTransaction::create([
            'order_no'             => 'SO-2026-012',
            'customer_name'        => 'Lim Grocery Trading',
            'total_amount'         => 89200,
            'payment_method'       => 'Installment',
            'status'               => 'Pending',
            'is_posted_to_finance' => false,
        ]);

        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-024'],
            [
                'customer_id'  => $customerMap['Lim Grocery Trading'],
                'type'         => 'invoice',
                'invoice_date' => '2026-07-15',
                'due_date'     => '2026-08-14',
                'currency'     => 'PHP',
                'subtotal'     => 79642.86,
                'vat_amount'   => 9557.14,
                'total'        => 89200,
                'status'       => 'sent',
                'notes'        => 'Grocery wholesale - rice and canned goods, installment terms',
            ]
        );

        $je4 = JournalEntry::create([
            'transaction_date' => '2026-07-15',
            'reference_no'     => 'JE-2026-015',
            'description'      => 'Down payment - Lim Grocery Trading (installment sale)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $cash, 'description' => 'Down payment received', 'debit' => 30000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $ar, 'description' => 'AR - Lim Grocery (remaining balance)', 'debit' => 59200, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $revenue, 'description' => 'Sales revenue', 'debit' => 0, 'credit' => 79642.86]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $vatOutput, 'description' => 'Output VAT 12%', 'debit' => 0, 'credit' => 9557.14]);

        // === 5: Garcia Auto Parts — Auto Parts Wholesale (Paid - Card) ===

        $st5 = SalesTransaction::create([
            'order_no'             => 'SO-2026-013',
            'customer_name'        => 'Garcia Auto Parts',
            'total_amount'         => 245000,
            'payment_method'       => 'Credit Card',
            'status'               => 'Paid',
            'is_posted_to_finance' => true,
        ]);

        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-025'],
            [
                'customer_id'  => $customerMap['Garcia Auto Parts'],
                'type'         => 'invoice',
                'invoice_date' => '2026-07-18',
                'due_date'     => '2026-08-17',
                'currency'     => 'PHP',
                'subtotal'     => 218750.00,
                'vat_amount'   => 26250.00,
                'total'        => 245000,
                'status'       => 'paid',
                'notes'        => 'Auto parts wholesale - brake systems and batteries',
            ]
        );

        $je5 = JournalEntry::create([
            'transaction_date' => '2026-07-18',
            'reference_no'     => 'JE-2026-016',
            'description'      => 'Sales revenue - Garcia Auto Parts (card payment)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $cash, 'description' => 'Card settlement - Garcia Auto Parts', 'debit' => 245000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $revenue, 'description' => 'Sales revenue', 'debit' => 0, 'credit' => 218750.00]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $vatOutput, 'description' => 'Output VAT 12%', 'debit' => 0, 'credit' => 26250.00]);
    }
}
