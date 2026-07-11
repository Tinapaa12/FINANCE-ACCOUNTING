<?php // Migration: creates chart_of_accounts table with self-referencing parent hierarchy. Stores code, name, type, normal balance, and status.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->string('account_code', 20)->unique();
            $table->string('account_name', 255);
            $table->enum('normal_balance', ['Debit', 'Credit']);
            $table->unsignedBigInteger('parent_account_id')->nullable();
            $table->enum('type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            $table->foreign('parent_account_id')
                  ->references('account_id')
                  ->on('chart_of_accounts')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};