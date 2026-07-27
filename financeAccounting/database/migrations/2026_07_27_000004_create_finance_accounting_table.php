<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_accounting', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('generated_at')->nullable();
            $table->string('status')->default('final');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['report_type', 'period_start', 'period_end'], 'fa_type_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_accounting');
    }
};
