<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('item_name')->nullable()->after('supplier');
            $table->integer('qty')->nullable()->after('item_name');
            $table->decimal('unit_cost', 15, 2)->nullable()->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'qty', 'unit_cost']);
        });
    }
};
