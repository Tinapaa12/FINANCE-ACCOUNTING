<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('computed_financial_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('label');
            $table->string('section')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('debit', 15, 2)->default(0)->nullable();
            $table->decimal('credit', 15, 2)->default(0)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['report_type', 'period_start', 'period_end'], 'cfr_type_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computed_financial_reports');
    }
};
