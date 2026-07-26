<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ManagementBudgetController;
use App\Http\Controllers\Api\DemoDataController;
use Illuminate\Http\Request;

require __DIR__ . '/auth.php';

Route::middleware('app.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    require __DIR__ . '/general-ledger.php';
    require __DIR__ . '/accounts-payable.php';
    require __DIR__ . '/accounts-receivable.php';
    require __DIR__ . '/procurement.php';
    require __DIR__ . '/financial-reports.php';

    Route::get('/api-playground', function () {
        return view('api-playground', [
            'accounts' => \App\Models\GeneralLedger\ChartOfAccount::where('status', 'Active')->orderBy('account_code')->get(),
            'periods'  => \App\Models\FinancialReporting\BudgetVsActual::select('report_period_start')
                ->get()->map(fn ($b) => \Carbon\Carbon::parse($b->report_period_start)->format('F Y'))
                ->unique()->sort()->values(),
        ]);
    })->name('api-playground');

    Route::post('/api-playground', function (Request $request) {
        $apiReq = Request::create('/api/management/budget', 'POST', $request->only(['account_code', 'budget_amount', 'period']));
        $apiReq->headers->set('X-API-Key', config('app.management_api_key'));
        $res = (new ManagementBudgetController)->store($apiReq);
        $data = $res->getData();
        if ($res->getStatusCode() === 201) {
            return redirect()->route('api-playground')->with('success', 'Budget entry created via API!');
        }
        return redirect()->route('api-playground')->with('error', $data->message ?? 'API error');
    })->name('api-playground.submit');

    Route::post('/api-playground/seed', function () {
        $apiReq = Request::create('/api/seed-demo', 'POST');
        $apiReq->headers->set('X-API-Key', config('app.management_api_key'));
        $res = (new DemoDataController)->seed($apiReq);
        $data = $res->getData();
        if ($res->getStatusCode() === 200) {
            return redirect()->route('api-playground')->with('success', 'Demo data seeded! ' . json_encode($data->stats ?? []));
        }
        return redirect()->route('api-playground')->with('error', $data->message ?? 'Seed API error');
    })->name('api-playground.seed');

    Route::post('/api-playground/migrate-fresh', function () {
        $apiReq = Request::create('/api/migrate-fresh', 'POST');
        $apiReq->headers->set('X-API-Key', config('app.management_api_key'));
        $res = (new DemoDataController)->migrateFresh($apiReq);
        $data = $res->getData();
        if ($res->getStatusCode() === 200) {
            return redirect()->route('api-playground')->with('success', 'migrate:fresh --seed done! Tables rebuilt and seeded.');
        }
        return redirect()->route('api-playground')->with('error', $data->message ?? 'Migration error');
    })->name('api-playground.migrate-fresh');
});
