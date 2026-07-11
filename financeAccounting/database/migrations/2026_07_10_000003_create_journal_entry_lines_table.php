<?php // Migration: creates journal_entry_lines table. Each line belongs to a journal entry and an account, with debit/credit amounts. Cascade deletes with parent.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id('line_id');
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('account_id');
            $table->string('description', 500)->nullable();
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('journal_entry_id')
                  ->references('journal_entry_id')
                  ->on('journal_entries')
                  ->onDelete('cascade');

            $table->foreign('account_id')
                  ->references('account_id')
                  ->on('chart_of_accounts')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};