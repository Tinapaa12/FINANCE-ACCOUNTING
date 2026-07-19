<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->string('item_name')->nullable()->after('purchase_order_id');
            $table->integer('qty_ordered')->nullable()->after('item_name');
            $table->integer('qty_received')->nullable()->after('qty_ordered');
        });
    }

    public function down(): void
    {
        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'qty_ordered', 'qty_received']);
        });
    }
};
