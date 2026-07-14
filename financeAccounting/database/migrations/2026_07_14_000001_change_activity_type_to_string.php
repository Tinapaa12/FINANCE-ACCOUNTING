<?php // Migration: changes activity_type from enum to varchar to allow "Cash In" / "Cash Out"
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE cash_flow_report_lines MODIFY COLUMN activity_type VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cash_flow_report_lines MODIFY COLUMN activity_type VARCHAR(50) NOT NULL");
    }
};
