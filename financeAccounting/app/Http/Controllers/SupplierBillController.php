<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierBill;

class SupplierBillController extends Controller
{
    public function index()
    {
        $supplierBills = SupplierBill::orderBy('due_date')->get();

        $upcomingBills = $supplierBills->take(4);

        $totalBillsAmount = SupplierBill::sum('amount');
        $totalBillsCount = SupplierBill::count();

        $paidThisMonthAmount = SupplierBill::where('status', 'Paid')
            ->whereMonth('updated_at', now()->month)
            ->sum('amount');

        $paidThisMonthCount = SupplierBill::where('status', 'Paid')
            ->whereMonth('updated_at', now()->month)
            ->count();

        $paymentsTodayAmount = SupplierBill::where('status', 'Paid')
            ->whereDate('updated_at', today())
            ->sum('amount');

        $paymentsTodayCount = SupplierBill::where('status', 'Paid')
            ->whereDate('updated_at', today())
            ->count();

        $pendingBillsAmount = SupplierBill::where('status', 'Pending')
            ->sum('amount');

        $pendingBillsCount = SupplierBill::where('status', 'Pending')
            ->count();

        return view('supplier-bills.index', compact(
            'supplierBills',
            'upcomingBills',
            'totalBillsAmount',
            'totalBillsCount',
            'paidThisMonthAmount',
            'paidThisMonthCount',
            'paymentsTodayAmount',
            'paymentsTodayCount',
            'pendingBillsAmount',
            'pendingBillsCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier' => 'required',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'status' => 'required',
        ]);

        $nextId = SupplierBill::count() + 1;

        SupplierBill::create([
            'bill_no' => 'BILL-' . str_pad($nextId, 2, '0', STR_PAD_LEFT),
            'po_no'   => 'PO-2026-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'grn_no'  => 'GRN-2026-' . str_pad($nextId, 3, '0', STR_PAD_LEFT),
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);

        return redirect()->route('supplier-bills.index');
    }

    public function destroy(SupplierBill $supplierBill)
    {
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
            'status' => 'required',
        ]);

        $supplierBill->update([
            'supplier' => $request->supplier,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);

        return redirect()->route('supplier-bills.index');
    }
}
