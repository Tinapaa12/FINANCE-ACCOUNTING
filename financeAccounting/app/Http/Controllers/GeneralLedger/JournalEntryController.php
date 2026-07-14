<?php // JournalEntryController — manages journal entries and their lines. Handles listing, creating, updating, and deleting journal entries within database transactions to ensure balanced entries.
namespace App\Http\Controllers\GeneralLedger;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function pdf()
    {
        $entries = JournalEntry::with(['lines.account'])->latest()->get();
        $accounts = ChartOfAccount::where('status', 'Active')->orderBy('account_code')
            ->get(['account_id', 'account_code', 'account_name']);
        return view('general-ledger.pdf.journal-entries', compact('entries', 'accounts'));
    }

    public function index()
    {
        $entries = JournalEntry::with(['lines.account', 'salesTransaction'])->latest()->paginate(10);
        $accounts = ChartOfAccount::where('status', 'Active')->orderBy('account_code')->get(['account_id', 'account_code', 'account_name']);
        return view('general-ledger.journal-entries.index', compact('entries', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'reference_no' => 'required|string|max:50|unique:journal_entries',
            'description' => 'required|string|max:500',
            'status' => 'required|in:Draft,Posted',
            'lines' => 'nullable|array|min:2',
            'lines.*.account_id' => 'required_with:lines|exists:chart_of_accounts,account_id',
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

            if (!empty($validated['lines'])) {
                foreach ($validated['lines'] as $line) {
                    if (empty($line['account_id'])) continue;
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->journal_entry_id,
                        'account_id' => $line['account_id'],
                        'description' => $line['description'] ?? $validated['description'],
                        'debit' => $line['debit'] ?? 0,
                        'credit' => $line['credit'] ?? 0,
                    ]);
                }
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('journal-entries.index')->with('success', 'Journal entry created successfully.');
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'reference_no' => 'nullable|string|max:50',
            'description' => 'required|string|max:500',
            'status' => 'required|in:Draft,Posted',
            'lines' => 'nullable|array|min:2',
            'lines.*.account_id' => 'required_with:lines|exists:chart_of_accounts,account_id',
            'lines.*.description' => 'nullable|string|max:500',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $journalEntry) {
            $journalEntry->update([
                'transaction_date' => $validated['transaction_date'],
                'reference_no' => $validated['reference_no'] ?? $journalEntry->reference_no,
                'description' => $validated['description'],
                'status' => $validated['status'],
            ]);

            if (!empty($validated['lines'])) {
                $journalEntry->lines()->delete();
                foreach ($validated['lines'] as $line) {
                    if (empty($line['account_id'])) continue;
                    JournalEntryLine::create([
                        'journal_entry_id' => $journalEntry->journal_entry_id,
                        'account_id' => $line['account_id'],
                        'description' => $line['description'] ?? $validated['description'],
                        'debit' => $line['debit'] ?? 0,
                        'credit' => $line['credit'] ?? 0,
                    ]);
                }
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('journal-entries.index')->with('success', 'Journal entry updated successfully.');
    }

    public function destroy(Request $request, JournalEntry $journalEntry)
    {
        $journalEntry->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('journal-entries.index')->with('success', 'Journal entry deleted successfully.');
    }
}
