<?php // Migration: creates tax_records table. Stores tax type, taxable amount, rate, computed tax, period, and filing status.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_records', function (Blueprint $table) {
            $table->id('tax_record_id');
            $table->string('reference_type'); // Customer Invoice, Supplier Bill, Refund
            $table->unsignedBigInteger('reference_id'); // ID of the related transaction (invoice/bill)
            $table->string('tax_type');        // VAT, EWT, etc.
            $table->decimal('taxable_amount', 15, 2);
            $table->decimal('tax_rate', 5, 2); // percentage, e.g. 12.00
            $table->decimal('tax_amount', 15, 2);
            $table->string('tax_period');      // Monthly, Quarterly, or "July 2026"
            $table->enum('filing_status', ['pending', 'filed', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_records');
    }
};
