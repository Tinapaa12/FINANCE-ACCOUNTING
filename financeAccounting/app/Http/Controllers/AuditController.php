<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('loggable')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('action', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('user', 'like', "%{$s}%");
            });
        }

        if ($request->filled('model')) {
            $query->where('loggable_type', 'App\\Models\\' . $request->model);
        }

        $logs = $query->paginate(20);
        return view('audit.index', compact('logs'));
    }
}
