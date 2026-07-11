<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_statements', function (Blueprint $table) {
            $table->id('income_statement_id');
            $table->foreignId('report_id')->constrained('financial_reports', 'report_id')->cascadeOnDelete();
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_statements');
    }
};
