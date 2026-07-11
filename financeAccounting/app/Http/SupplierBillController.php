<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierBillController extends Controller
{
    public function index()
    {
        return view('supplier-bills.index');
    }
}