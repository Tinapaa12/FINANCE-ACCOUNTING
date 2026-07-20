<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_date',
                'due_date',
                'invoice_type',
                'currency',
                'subtotal',
                'vat_amount',
                'line_items',
                'description',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('invoice_type', 50)->default('Invoice (Standard)');
            $table->string('currency', 10)->default('PHP');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->json('line_items')->nullable();
            $table->text('description')->nullable();
        });
    }
};
