<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flow_report_lines', function (Blueprint $table) {
            $table->id('cash_flow_line_id');
            $table->foreignId('cash_flow_id')->constrained('cash_flow_reports', 'cash_flow_id')->cascadeOnDelete();
            $table->enum('activity_type', ['Operating', 'Investing', 'Financing']);
            $table->string('line_name');
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('line_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flow_report_lines');
    }
};
