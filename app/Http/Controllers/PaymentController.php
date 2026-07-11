<?php

namespace App\Http\Controllers;

use App\Models\SupplierBill;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = SupplierBill::where('status', 'Paid')->get();

        return view('payments.index', compact('payments'));
    }
}