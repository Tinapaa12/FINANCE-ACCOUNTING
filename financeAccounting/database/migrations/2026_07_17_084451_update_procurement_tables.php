<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Draft'");

        DB::table('purchase_orders')->where('status', 'Pending')->update(['status' => 'Draft']);
        DB::table('purchase_orders')->where('status', 'Approved')->update(['status' => 'Confirmed']);
        DB::table('purchase_orders')->where('status', 'Received')->update(['status' => 'Delivered']);

        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('Draft','Sent','Confirmed','Delivered','Cancelled') NOT NULL DEFAULT 'Draft'");

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('expected_delivery');
            $table->timestamp('confirmed_at')->nullable()->after('sent_at');
            $table->timestamp('delivered_at')->nullable()->after('confirmed_at');
        });

        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->string('po_no_ref')->nullable()->after('purchase_order_id');
            $table->unsignedBigInteger('supplier_bill_id')->nullable()->after('po_no_ref');
        });

        Schema::table('supplier_bills', function (Blueprint $table) {
            $table->enum('matching_status', ['Unmatched','Partially Matched','Matched','Flagged'])->default('Unmatched')->after('status');
            $table->text('matching_notes')->nullable()->after('matching_status');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('Pending','Approved','Received','Cancelled') NOT NULL DEFAULT 'Pending'");

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['sent_at', 'confirmed_at', 'delivered_at']);
        });

        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->dropColumn(['po_no_ref', 'supplier_bill_id']);
        });

        Schema::table('supplier_bills', function (Blueprint $table) {
            $table->dropColumn(['matching_status', 'matching_notes']);
        });
    }
};
