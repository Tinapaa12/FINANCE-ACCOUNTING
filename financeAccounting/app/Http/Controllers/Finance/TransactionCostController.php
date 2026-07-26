<?php // TransactionCostController — provides a Blade form simulating the Supply Chain module that posts to the existing Supplier Bills API.
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;

class TransactionCostController extends Controller
{
    public function create()
    {
        return view('supply-chain.create');
    }
}
