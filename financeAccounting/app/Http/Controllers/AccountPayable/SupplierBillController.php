<?php

namespace App\Http\Controllers\AccountPayable;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\SupplierBill;
use App\Models\AccountPayable\PurchaseOrder;
use App\Models\AccountPayable\GoodsReceivedNote;
use App\Models\Attachment;
use App\Services\AccountPayableService;
use Illuminate\Http\Request;

class SupplierBillController extends Controller
{
    public function __construct(
        private readonly AccountPayableService $apService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'due_date');
        $direction = $request->input('direction', 'asc');
        $filterStatus = $request->input('filter_status');
        $filterMethod = $request->input('filter_method');
        $tab = $request->input('tab', 'pos');

        $query = SupplierBill::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('supplier', 'like', "%{$search}%")
                  ->orWhere('bill_no', 'like', "%{$search}%")
                  ->orWhere('po_no', 'like', "%{$search}%")
                  ->orWhere('grn_no', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        if ($filterMethod) {
            $query->where('payment_method', $filterMethod);
        }

        $supplierBills = $query->with(['attachments', 'payments'])->orderBy($sort, $direction)->paginate(5)->withQueryString();

        $metrics = $this->apService->getDashboardMetrics();

        $poSearch = $request->input('po_search');
        $purchaseOrders = PurchaseOrder::when($poSearch, function ($q) use ($poSearch) {
            $q->where(function ($q) use ($poSearch) {
                $q->where('po_no', 'like', "%{$poSearch}%")
                  ->orWhere('supplier', 'like', "%{$poSearch}%")
                  ->orWhere('status', 'like', "%{$poSearch}%");
            });
        })->orderBy('created_at', 'desc')->paginate(6)->withQueryString();

        $grnSearch = $request->input('grn_search');
        $grns = GoodsReceivedNote::with('purchaseOrder')
            ->when($grnSearch, function ($q) use ($grnSearch) {
                $q->where(function ($q) use ($grnSearch) {
                    $q->where('grn_no', 'like', "%{$grnSearch}%")
                      ->orWhere('supplier', 'like', "%{$grnSearch}%")
                      ->orWhere('status', 'like', "%{$grnSearch}%");
                });
            })->orderBy('created_at', 'desc')->paginate(6)->withQueryString();

        $allPOs = PurchaseOrder::whereIn('status', ['Confirmed', 'Delivered'])->orderBy('po_no')->get();
        $allGRNs = GoodsReceivedNote::whereIn('status', ['Completed', 'Pending'])->orderBy('grn_no')->get();

        extract($metrics);

        $varNames = array_merge(['supplierBills'], array_keys($metrics), [
            'search', 'sort', 'direction', 'filterStatus', 'filterMethod',
            'poSearch', 'grnSearch', 'tab', 'purchaseOrders', 'grns', 'allPOs', 'allGRNs',
        ]);

        return view('account-payable::supplier-bills.index', compact(...$varNames));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier' => 'required',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'status' => 'required|in:Pending,Approved,Paid',
            'payment_method' => 'nullable|string',
            'ewt_rate' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'nullable|string',
            'po_no' => 'nullable|string|max:50',
            'grn_no' => 'nullable|string|max:50',
        ]);

        $nextId = SupplierBill::count() + 1;

        $bill = SupplierBill::create([
            'bill_no' => 'BILL-' . str_pad($nextId, 2, '0', STR_PAD_LEFT),
            'po_no'   => $request->po_no,
            'grn_no'  => $request->grn_no,
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'ewt_rate' => $request->ewt_rate,
            'payment_terms' => $request->payment_terms,
        ]);

        \audit_log($bill, 'created', "Supplier bill #{$bill->bill_no} created for {$bill->supplier}");
        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }

    public function approve($id)
    {
        $this->apService->approveBill($id);
        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }

    public function pay(Request $request, SupplierBill $supplierBill)
    {
        $this->apService->payBill($supplierBill);

        if ($request->wantsJson()) {
            $metrics = $this->apService->getDashboardMetrics();
            $totalOutstanding = SupplierBill::where('status', '!=', 'Paid')->sum('amount');

            return response()->json(array_merge(
                ['success' => true],
                $metrics,
                [
                    'totalOutstanding' => $totalOutstanding,
                    'upcomingBills' => $metrics['upcomingBills']->map(fn($b) => [
                        'id' => $b->id,
                        'supplier' => $b->supplier,
                        'bill_no' => $b->bill_no,
                        'due_date' => $b->due_date,
                        'amount' => $b->amount,
                        'diff' => \Carbon\Carbon::parse($b->due_date)->diffForHumans(),
                    ]),
                    'overdueBills' => $metrics['overdueBills']->map(fn($b) => [
                        'id' => $b->id,
                        'supplier' => $b->supplier,
                        'bill_no' => $b->bill_no,
                        'due_date' => $b->due_date,
                        'amount' => $b->amount,
                        'overdueDays' => now()->diffInDays(\Carbon\Carbon::parse($b->due_date)),
                    ]),
                ]
            ));
        }

        return back();
    }

    public function batchPay(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'No bills selected.'])
                : back()->with('error', 'No bills selected.');
        }

        $this->apService->batchPayBills($ids);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }

    public function destroy(SupplierBill $supplierBill)
    {
        \audit_log($supplierBill, 'deleted', "Supplier bill #{$supplierBill->bill_no} deleted");
        $supplierBill->delete();

        $remaining = SupplierBill::orderBy('id')->get();
        foreach ($remaining as $i => $bill) {
            $num = $i + 1;
            $bill->update([
                'bill_no' => 'BILL-' . str_pad($num, 2, '0', STR_PAD_LEFT),
                'po_no'   => 'PO-2026-' . str_pad($num, 3, '0', STR_PAD_LEFT),
                'grn_no'  => 'GRN-2026-' . str_pad($num, 3, '0', STR_PAD_LEFT),
            ]);
        }

        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }

    public function update(Request $request, SupplierBill $supplierBill)
    {
        $request->validate([
            'supplier' => 'required',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'status' => 'required|in:Pending,Approved,Paid',
            'payment_method' => 'nullable|string',
            'ewt_rate' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'nullable|string',
        ]);

        $old = $supplierBill->getOriginal();
        $supplierBill->update([
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'ewt_rate' => $request->ewt_rate,
            'payment_terms' => $request->payment_terms,
        ]);

        \audit_log($supplierBill, 'updated', "Supplier bill #{$supplierBill->bill_no} updated", $old, $supplierBill->toArray());
        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }

    public function uploadAttachment(Request $request, $id)
    {
        $bill = SupplierBill::findOrFail($id);
        $request->validate(['file' => 'required|file|max:10240']);
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('attachments', $filename, 'public');
        $bill->attachments()->create([
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }

    public function downloadAttachment($id)
    {
        $attachment = \App\Models\Attachment::findOrFail($id);
        $path = storage_path('app/public/attachments/' . $attachment->filename);
        return response()->download($path, $attachment->original_filename);
    }

    public function markAsPaid($id)
    {
        $bill = SupplierBill::findOrFail($id);
        $bill->status = 'Paid';
        $bill->save();
        return redirect()->route('supplier-bills.index', ['tab' => 'bills']);
    }
}
