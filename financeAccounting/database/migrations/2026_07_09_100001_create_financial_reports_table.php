<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->string('report_type'); // Income Statement, Balance Sheet, Cash Flow Statement, Trial Balance
            $table->date('report_period_start');
            $table->date('report_period_end');
            $table->dateTime('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
