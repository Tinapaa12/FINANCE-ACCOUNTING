<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_sheets', function (Blueprint $table) {
            $table->id('balance_sheet_id');
            $table->foreignId('report_id')->constrained('financial_reports', 'report_id')->cascadeOnDelete();
            $table->string('statement_title')->default('Balance Sheet');
            $table->string('period_label'); // e.g. "As of June 30, 2026"
            $table->dateTime('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_sheets');
    }
};
