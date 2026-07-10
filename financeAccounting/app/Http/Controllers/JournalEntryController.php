<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index()
    {
        $entries = JournalEntry::with(['lines.account'])->latest()->get();
        return view('journal-entries.index', compact('entries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'reference_no' => 'required|string|max:50|unique:journal_entries',
            'description' => 'required|string|max:500',
            'status' => 'required|in:Draft,Posted',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,account_id',
            'lines.*.description' => 'nullable|string|max:500',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $entry = JournalEntry::create([
                'transaction_date' => $validated['transaction_date'],
                'reference_no' => $validated['reference_no'],
                'description' => $validated['description'],
                'status' => $validated['status'],
            ]);

            foreach ($validated['lines'] as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->journal_entry_id,
                    'account_id' => $line['account_id'],
                    'description' => $line['description'] ?? $validated['description'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                ]);
            }
        });

        return redirect()->route('journal-entries.index')->with('success', 'Journal entry created successfully.');
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:500',
            'status' => 'required|in:Draft,Posted',
        ]);

        $journalEntry->update($validated);
        return redirect()->route('journal-entries.index')->with('success', 'Journal entry updated successfully.');
    }

    public function destroy(JournalEntry $journalEntry)
    {
        $journalEntry->delete();
        return redirect()->route('journal-entries.index')->with('success', 'Journal entry deleted successfully.');
    }
}