<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->string('invoice_type', 50)->nullable()->after('status');
            $table->date('invoice_date')->nullable()->after('invoice_type');
            $table->date('due_date')->nullable()->after('invoice_date');
            $table->string('currency', 3)->nullable()->after('due_date');
            $table->decimal('subtotal', 15, 2)->nullable()->after('currency');
            $table->decimal('vat_amount', 15, 2)->nullable()->after('subtotal');
            $table->text('line_items')->nullable()->after('vat_amount');
            $table->text('description')->nullable()->after('line_items');
        });
    }

    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropColumn(['invoice_type', 'invoice_date', 'due_date', 'currency', 'subtotal', 'vat_amount', 'line_items', 'description']);
        });
    }
};
