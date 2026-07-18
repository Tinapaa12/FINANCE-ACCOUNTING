<?php // Migration: creates supplier_bills table for Accounts Payable. Tracks bill number, PO/GRN, supplier, amount, due date, and status.
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
Schema::create('supplier_bills', function (Blueprint $table) {
    $table->id();
    $table->string('bill_no');
    $table->string('po_no');
    $table->string('grn_no');
    $table->string('supplier');
    $table->decimal('amount', 12, 2);
    $table->date('due_date');
    $table->enum('status', ['Pending', 'Approved', 'Paid']);
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_bills');
    }
};
