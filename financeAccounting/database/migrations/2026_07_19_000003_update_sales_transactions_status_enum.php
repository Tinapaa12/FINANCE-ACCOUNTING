<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Paid'])->default('Pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->enum('status', ['Draft', 'Sent', 'Overdue', 'Cleared', 'Paid'])->default('Draft')->change();
        });
    }
};
