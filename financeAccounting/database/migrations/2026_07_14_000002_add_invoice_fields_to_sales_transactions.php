<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->date('invoice_date')->nullable()->after('order_no');
            $table->date('due_date')->nullable()->after('invoice_date');
            $table->string('invoice_type', 50)->nullable()->after('customer_name');
            $table->string('currency', 10)->default('PHP')->after('invoice_type');
            $table->decimal('subtotal', 15, 2)->nullable()->after('total_amount');
            $table->decimal('vat_amount', 15, 2)->nullable()->after('subtotal');
            $table->json('line_items')->nullable()->after('vat_amount');
            $table->text('description')->nullable()->after('line_items');
        });

        DB::statement("ALTER TABLE sales_transactions MODIFY COLUMN status ENUM('Draft','Sent','Overdue','Cleared','Paid') NOT NULL DEFAULT 'Draft'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE sales_transactions MODIFY COLUMN status ENUM('Pending','Paid') NOT NULL DEFAULT 'Pending'");

        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropColumn(['invoice_date', 'due_date', 'invoice_type', 'currency', 'subtotal', 'vat_amount', 'line_items', 'description']);
        });
    }
};
