<?php // Migration: creates budget_vs_actuals table. Stores account-level budget vs actual data with variance and status.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_vs_actuals', function (Blueprint $table) {
            $table->id('budget_actual_id');
            $table->unsignedBigInteger('account_id')->nullable(); // will FK to chart_of_accounts later
            $table->string('account_name');
            $table->date('report_period_start');
            $table->date('report_period_end');
            $table->decimal('budget_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2);
            $table->decimal('variance_amount', 15, 2);
            $table->enum('status', ['Over Budget', 'Under Budget', 'On Budget'])->default('On Budget');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_vs_actuals');
    }
};
