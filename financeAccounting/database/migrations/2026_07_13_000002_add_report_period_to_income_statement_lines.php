<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('income_statement_lines', function (Blueprint $table) {
            $table->date('report_period_start')->nullable()->after('account_id');
            $table->date('report_period_end')->nullable()->after('report_period_start');
        });
    }

    public function down(): void
    {
        Schema::table('income_statement_lines', function (Blueprint $table) {
            $table->dropColumn(['report_period_start', 'report_period_end']);
        });
    }
};
