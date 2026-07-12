<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierBill;
use App\Models\Attachment;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceivedNote;

class SupplierBillController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'due_date');
        $direction = $request->input('direction', 'asc');
        $filterStatus = $request->input('filter_status');
        $filterMethod = $request->input('filter_method');
        $tab = $request->input('tab', 'bills');

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

        $supplierBills = $query->with(['attachments', 'payments'])->orderBy($sort, $direction)->paginate(6)->withQueryString();

        $upcomingBills = SupplierBill::where('status', 'Pending')
            ->orderBy('due_date')
            ->get();

        $overdueBills = SupplierBill::whereIn('status', ['Pending', 'Approved'])
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get();

        $totalBillsAmount = SupplierBill::where('status', '!=', 'Paid')->sum('amount');
        $totalBillsCount = SupplierBill::where('status', '!=', 'Paid')->count();

        $paidThisMonthAmount = SupplierBill::where('status', 'Paid')
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        $paidThisMonthCount = SupplierBill::where('status', 'Paid')
            ->whereMonth('paid_at', now()->month)
            ->count();

        $paymentsTodayAmount = SupplierBill::where('status', 'Paid')
            ->whereDate('paid_at', today())
            ->sum('amount');

        $paymentsTodayCount = SupplierBill::where('status', 'Paid')
            ->whereDate('paid_at', today())
            ->count();

        $pendingBillsAmount = SupplierBill::where('status', 'Pending')
            ->sum('amount');

        $pendingBillsCount = SupplierBill::where('status', 'Pending')
            ->count();

        $overdueAmount = SupplierBill::whereIn('status', ['Pending', 'Approved'])
            ->whereDate('due_date', '<', now())
            ->sum('amount');

        $overdueCount = SupplierBill::whereIn('status', ['Pending', 'Approved'])
            ->whereDate('due_date', '<', now())
            ->count();

        $purchaseOrders = PurchaseOrder::orderBy('created_at', 'desc')->paginate(6)->withQueryString();
        $grns = GoodsReceivedNote::with('purchaseOrder')->orderBy('created_at', 'desc')->paginate(6)->withQueryString();
        $allPOs = PurchaseOrder::whereIn('status', ['Approved', 'Received'])->orderBy('po_no')->get();

        return view('supplier-bills.index', compact(
            'supplierBills',
            'upcomingBills',
            'overdueBills',
            'totalBillsAmount',
            'totalBillsCount',
            'paidThisMonthAmount',
            'paidThisMonthCount',
            'paymentsTodayAmount',
            'paymentsTodayCount',
            'pendingBillsAmount',
            'pendingBillsCount',
            'overdueAmount',
            'overdueCount',
            'search',
            'sort',
            'direction',
            'filterStatus',
            'filterMethod',
            'tab',
            'purchaseOrders',
            'grns',
            'allPOs',
        ));
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
        ]);

        $nextId = SupplierBill::count() + 1;

        $bill = SupplierBill::create([
            'bill_no' => 'BILL-' . str_pad($nextId, 2, '0', STR_PAD_LEFT),
            'po_no'   => 'PO-2026-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'grn_no'  => 'GRN-2026-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'ewt_rate' => $request->ewt_rate,
            'payment_terms' => $request->payment_terms,
        ]);

        audit_log($bill, 'created', "Supplier bill #{$bill->bill_no} created for {$bill->supplier}");
        return redirect()->route('supplier-bills.index');
    }

    public function approve($id)
    {
        $bill = SupplierBill::findOrFail($id);
        $bill->update([
            'status' => 'Approved',
            'approved_at' => now(),
            'approved_by' => auth()->user()->name ?? 'Manager',
        ]);
        audit_log($bill, 'approved', "Supplier bill #{$bill->bill_no} approved");
        return redirect()->route('supplier-bills.index');
    }

    public function batchPay(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'No bills selected.'])
                : back()->with('error', 'No bills selected.');
        }

        $bills = SupplierBill::whereIn('id', $ids)->where('status', 'Approved')->get();
        foreach ($bills as $bill) {
            $bill->update(['status' => 'Paid', 'paid_at' => now()]);
            audit_log($bill, 'paid', "Supplier bill #{$bill->bill_no} paid via batch payment");
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('supplier-bills.index');
    }

public function pay(Request $request, SupplierBill $supplierBill)
{
    $supplierBill->update(['status' => 'Paid', 'paid_at' => now()]);
    audit_log($supplierBill, 'paid', "Supplier bill #{$supplierBill->bill_no} marked as paid");

    if ($request->wantsJson()) {
        $paidThisMonthAmount = SupplierBill::where('status', 'Paid')
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');
        $paidThisMonthCount = SupplierBill::where('status', 'Paid')
            ->whereMonth('paid_at', now()->month)
            ->count();
        $paymentsTodayAmount = SupplierBill::where('status', 'Paid')
            ->whereDate('paid_at', today())
            ->sum('amount');
        $paymentsTodayCount = SupplierBill::where('status', 'Paid')
            ->whereDate('paid_at', today())
            ->count();
        $pendingBillsAmount = SupplierBill::where('status', 'Pending')
            ->sum('amount');
        $pendingBillsCount = SupplierBill::where('status', 'Pending')
            ->count();
        $upcomingBills = SupplierBill::where('status', 'Pending')
            ->orderBy('due_date')
            ->get();
        $totalOutstanding = SupplierBill::where('status', '!=', 'Paid')->sum('amount');

        $overdueBills = SupplierBill::whereIn('status', ['Pending', 'Approved'])
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get();
        $overdueAmount = $overdueBills->sum('amount');
        $overdueCount = $overdueBills->count();

        return response()->json([
            'success' => true,
            'paidThisMonthAmount' => $paidThisMonthAmount,
            'paidThisMonthCount' => $paidThisMonthCount,
            'paymentsTodayAmount' => $paymentsTodayAmount,
            'paymentsTodayCount' => $paymentsTodayCount,
            'pendingBillsAmount' => $pendingBillsAmount,
            'pendingBillsCount' => $pendingBillsCount,
            'overdueAmount' => $overdueAmount,
            'overdueCount' => $overdueCount,
            'upcomingBills' => $upcomingBills->map(fn($b) => [
                'id' => $b->id,
                'supplier' => $b->supplier,
                'bill_no' => $b->bill_no,
                'due_date' => $b->due_date,
                'amount' => $b->amount,
                'diff' => \Carbon\Carbon::parse($b->due_date)->diffForHumans(),
            ]),
            'overdueBills' => $overdueBills->map(fn($b) => [
                'id' => $b->id,
                'supplier' => $b->supplier,
                'bill_no' => $b->bill_no,
                'due_date' => $b->due_date,
                'amount' => $b->amount,
                'overdueDays' => now()->diffInDays(\Carbon\Carbon::parse($b->due_date)),
            ]),
            'totalOutstanding' => $totalOutstanding,
        ]);
    }

    return back();
}

    public function destroy(SupplierBill $supplierBill)
    {
        audit_log($supplierBill, 'deleted', "Supplier bill #{$supplierBill->bill_no} deleted");
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

        return redirect()->route('supplier-bills.index');
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

        audit_log($supplierBill, 'updated', "Supplier bill #{$supplierBill->bill_no} updated", $old, $supplierBill->toArray());
        return redirect()->route('supplier-bills.index');
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
        return redirect()->route('supplier-bills.index');
    }

    public function downloadAttachment($id)
    {
        $attachment = Attachment::findOrFail($id);
        $path = storage_path('app/public/attachments/' . $attachment->filename);
        return response()->download($path, $attachment->original_filename);
    }

    public function markAsPaid($id)
{
    $bill = SupplierBill::findOrFail($id);

    $bill->status = 'Paid';
    $bill->save();

    return redirect()->route('supplier-bills.index');
}


}
