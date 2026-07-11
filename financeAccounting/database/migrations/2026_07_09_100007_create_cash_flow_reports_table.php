<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flow_reports', function (Blueprint $table) {
            $table->id('cash_flow_id');
            $table->foreignId('report_id')->constrained('financial_reports', 'report_id')->cascadeOnDelete();
            $table->string('statement_title')->default('Cash Flow Statement');
            $table->string('period_label'); // e.g. "For the Month Ended June 2026"
            $table->dateTime('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flow_reports');
    }
};
