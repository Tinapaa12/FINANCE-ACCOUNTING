<?php

namespace App\Http\Controllers;

class ARController extends Controller
{
    public function overview()
    {
        return view('ar.overview');
    }

    public function payments()
    {
        return view('ar.payments');
    }

    public function aging()
    {
        return view('ar.aging');
    }
}
