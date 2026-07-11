<?php // Migration: creates balance_sheet_lines table. Each line belongs to a balance sheet with name, amount, and category.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_sheet_lines', function (Blueprint $table) {
            $table->id('balance_sheet_line_id');
            $table->foreignId('balance_sheet_id')->constrained('balance_sheets', 'balance_sheet_id')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->nullable(); // will FK to chart_of_accounts later
            $table->string('line_name');
            $table->enum('section', ['Asset', 'Liability', 'Equity']);
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('line_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_sheet_lines');
    }
};
