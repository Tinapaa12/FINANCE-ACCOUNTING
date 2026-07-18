<?php // Migration: creates trial_balances table linked to a financial report. Stores account-level debit/credit balances.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trial_balances', function (Blueprint $table) {
            $table->id('trial_balance_id');
            $table->foreignId('report_id')->constrained('financial_reports', 'report_id')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->nullable(); // will FK to chart_of_accounts later
            $table->string('account_name');
            $table->decimal('debit_amount', 15, 2)->nullable();
            $table->decimal('credit_amount', 15, 2)->nullable();
            $table->unsignedInteger('line_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_balances');
    }
};
