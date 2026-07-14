<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE supplier_bills MODIFY COLUMN status ENUM('Pending', 'Approved', 'Paid') NOT NULL DEFAULT 'Pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE supplier_bills MODIFY COLUMN status ENUM('Pending', 'Paid') NOT NULL DEFAULT 'Pending'");
    }
};
