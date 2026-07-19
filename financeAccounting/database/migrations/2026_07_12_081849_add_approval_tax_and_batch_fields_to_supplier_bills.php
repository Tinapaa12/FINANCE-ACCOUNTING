<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_bills', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('paid_at');
            $table->string('approved_by')->nullable()->after('approved_at');
            $table->decimal('ewt_rate', 5, 2)->nullable()->after('approved_by');
            $table->string('payment_terms')->nullable()->after('ewt_rate');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_bills', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'approved_by', 'ewt_rate', 'payment_terms']);
        });
    }
};
