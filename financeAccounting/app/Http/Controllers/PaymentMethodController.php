<?php

namespace App\Http\Controllers;

use App\Models\AccountsPayable\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::orderBy('name')->get();
        return view('accounts-payable.payment-methods.index', compact('methods'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:payment_methods,name']);
        PaymentMethod::create(['name' => $request->name]);
        return redirect()->route('payment-methods.index');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate(['name' => 'required|unique:payment_methods,name,' . $paymentMethod->id]);
        $paymentMethod->update(['name' => $request->name]);
        return redirect()->route('payment-methods.index');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->route('payment-methods.index');
    }
}
