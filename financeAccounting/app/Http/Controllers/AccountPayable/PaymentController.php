<?php

namespace App\Http\Controllers\AccountPayable;

use App\Http\Controllers\Controller;
use App\Models\AccountPayable\Payment;
use App\Models\AccountPayable\SupplierBill;
use App\Services\AccountPayableService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly AccountPayableService $apService
    ) {}

    public function index(Request $request)
    {
        $query = Payment::with('supplierBill');

        if ($search = $request->input('search')) {
            $query->whereHas('supplierBill', function ($q) use ($search) {
                $q->where('supplier', 'like', "%{$search}%")
                  ->orWhere('bill_no', 'like', "%{$search}%");
            })->orWhere('payment_method', 'like', "%{$search}%")
              ->orWhere('reference', 'like', "%{$search}%");
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('account-payable::payments.index', compact('payments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_bill_id' => 'required|exists:supplier_bills,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string',
        ]);

        try {
            $this->apService->recordPayment($request->only([
                'supplier_bill_id', 'amount', 'payment_method', 'payment_date', 'reference',
            ]));
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()->route('supplier-bills.index');
    }
}
