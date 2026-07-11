<?php

namespace App\Http\Controllers;

use App\Models\SupplierBill;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBillsAmount = SupplierBill::sum('amount');
        $totalBillsCount = SupplierBill::count();
        $pendingBillsCount = SupplierBill::where('status', 'Pending')->count();
        $paidThisMonthAmount = SupplierBill::where('status', 'Paid')
            ->whereMonth('updated_at', now()->month)
            ->sum('amount');

        return view('dashboard', compact(
            'totalBillsAmount',
            'totalBillsCount',
            'pendingBillsCount',
            'paidThisMonthAmount'
        ));
    }
}
