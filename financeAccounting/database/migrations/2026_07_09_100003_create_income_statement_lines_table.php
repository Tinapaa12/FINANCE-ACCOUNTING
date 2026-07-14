<?php // Migration: creates income_statement_lines table. Each line belongs to an income statement with name, amount, and type.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_statement_lines', function (Blueprint $table) {
            $table->id('income_statement_line_id');
            $table->foreignId('income_statement_id')->constrained('income_statements', 'income_statement_id')->cascadeOnDelete();
            // account_id will reference chart_of_accounts once the General Ledger module exists.
            // Left as a plain nullable column for now (no FK constraint) so this module works standalone.
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('line_name');       // e.g. Sales Revenue, Rent Expense
            $table->string('category');        // revenue, cost_of_goods, operating_expense, other_income
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('line_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_statement_lines');
    }
};
