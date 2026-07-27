<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->decimal('initial_payment', 15, 2)->default(0)->after('total_amount');
            $table->date('due_date')->nullable()->after('initial_payment');
        });

        Schema::getConnection()->statement("ALTER TABLE sales_transactions MODIFY COLUMN payment_method ENUM('Cash', 'Credit Card', 'Bank Transfer', 'Pay Later') NOT NULL DEFAULT 'Cash'");
    }

    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropColumn(['initial_payment', 'due_date']);
        });

        Schema::getConnection()->statement("ALTER TABLE sales_transactions MODIFY COLUMN payment_method ENUM('Cash', 'Credit Card', 'Bank Transfer', 'Installment') NOT NULL DEFAULT 'Cash'");
    }
};
