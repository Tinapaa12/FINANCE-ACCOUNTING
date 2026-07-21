<?php

namespace Database\Seeders;

use App\Models\GeneralLedger\ChartOfAccount;
use App\Models\GeneralLedger\JournalEntry;
use App\Models\GeneralLedger\JournalEntryLine;
use Illuminate\Database\Seeder;

class InventoryToGLSeeder extends Seeder
{
    public function run(): void
    {
        $coa = ChartOfAccount::pluck('account_id', 'account_code');

        $cash      = $coa['1010'] ?? null;
        $inventory = $coa['1200'] ?? null;
        $ap        = $coa['2100'] ?? null;
        $cogs      = $coa['5000'] ?? null;
        $expenses  = $coa['6200'] ?? null;

        if (!$inventory || !$cogs || !$cash || !$ap || !$expenses) {
            return;
        }

        // === 1: Inventory Purchase — 50 units of assorted hardware stock on credit ===
        // Dr Merchandise Inventory / Cr Accounts Payable

        $je1 = JournalEntry::create([
            'transaction_date' => '2026-07-03',
            'reference_no'     => 'JE-2026-017',
            'description'      => 'Inventory purchase - 50 units assorted hardware (wire, fasteners, tools) from Davao Hardware Supply',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $inventory, 'description' => 'Assorted hardware stock received', 'debit' => 95000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je1->journal_entry_id, 'account_id' => $ap, 'description' => 'Davao Hardware Supply - accounts payable', 'debit' => 0, 'credit' => 95000]);

        // === 2: Cost of Goods Sold Recognition — Sale of 20 inventory units ===
        // Dr Cost of Goods Sold / Cr Merchandise Inventory
        // (Triggered when a sales transaction is fulfilled from warehouse stock)

        $je2 = JournalEntry::create([
            'transaction_date' => '2026-07-08',
            'reference_no'     => 'JE-2026-018',
            'description'      => 'COGS recognition - 20 units of electronic components shipped to Santos Enterprises (SO-2026-004)',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je2->journal_entry_id, 'account_id' => $cogs, 'description' => 'Cost of goods sold - electronic components', 'debit' => 72000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je2->journal_entry_id, 'account_id' => $inventory, 'description' => 'Inventory out - electronic components', 'debit' => 0, 'credit' => 72000]);

        // === 3: Inventory Write-Off — Damaged goods from water leakage in warehouse ===
        // Dr Expenses (Loss on Write-off) / Cr Merchandise Inventory
        // (Approved by warehouse supervisor, recorded as an extraordinary loss)

        $je3 = JournalEntry::create([
            'transaction_date' => '2026-07-14',
            'reference_no'     => 'JE-2026-019',
            'description'      => 'Inventory write-off - 12 cartons of paper products damaged by warehouse water leakage',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $expenses, 'description' => 'Loss on inventory write-off (water damage)', 'debit' => 28350, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je3->journal_entry_id, 'account_id' => $inventory, 'description' => 'Damaged inventory written off', 'debit' => 0, 'credit' => 28350]);

        // === 4: Inventory Adjustment — Cycle count adjustment (found surplus) ===
        // Dr Merchandise Inventory / Cr Cost of Goods Sold
        // (Annual physical count found 5 more units of an item than system recorded)

        $je4 = JournalEntry::create([
            'transaction_date' => '2026-07-14',
            'reference_no'     => 'JE-2026-020',
            'description'      => 'Inventory adjustment - cycle count surplus: 5 units of office chairs found in excess of book balance',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $inventory, 'description' => 'Inventory surplus from cycle count', 'debit' => 42500, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je4->journal_entry_id, 'account_id' => $cogs, 'description' => 'COGS reduction - inventory overage', 'debit' => 0, 'credit' => 42500]);

        // === 5: Inter-Warehouse Transfer Cost — Transfer fee and logistics cost for moving stock ===
        // Dr Cost of Goods Sold / Cr Cash
        // (Transfer cost is capitalized into inventory cost as a COGS component)

        $je5 = JournalEntry::create([
            'transaction_date' => '2026-07-20',
            'reference_no'     => 'JE-2026-021',
            'description'      => 'Inventory transfer cost - logistics fee for moving 30 units of appliances from Manila warehouse to Cebu branch',
            'status'           => 'Posted',
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $cogs, 'description' => 'Freight and handling - inter-warehouse transfer', 'debit' => 15200, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je5->journal_entry_id, 'account_id' => $cash, 'description' => 'Logistics payment - 2GO Freight', 'debit' => 0, 'credit' => 15200]);
    }
}
