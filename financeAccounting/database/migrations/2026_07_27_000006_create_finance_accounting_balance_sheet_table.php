<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_accounting_balance_sheet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_accounting_id')->constrained('finance_accounting')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts', 'account_id')->nullOnDelete();
            $table->string('label');
            $table->string('section');
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_accounting_balance_sheet');
    }
};
