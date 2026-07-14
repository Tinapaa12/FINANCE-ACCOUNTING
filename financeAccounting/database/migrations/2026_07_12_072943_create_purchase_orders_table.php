<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_no')->unique();
            $table->string('supplier');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('order_date');
            $table->date('expected_delivery')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Received', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
