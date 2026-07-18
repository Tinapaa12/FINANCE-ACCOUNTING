<?php // Migration: creates sales_transactions table for the dummy Sales module. Tracks order number, customer, amount, payment method, status, and finance posting flag.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id('sales_transaction_id');
            $table->string('order_no', 20)->unique();
            $table->string('customer_name', 255);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->enum('payment_method', ['Cash', 'Credit Card', 'Bank Transfer', 'Installment']);
            $table->enum('status', ['Draft', 'Sent', 'Overdue', 'Cleared', 'Paid'])->default('Draft');
            $table->boolean('is_posted_to_finance')->default(false);
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_transactions');
    }
};
