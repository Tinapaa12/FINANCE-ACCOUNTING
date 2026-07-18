<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('cash_flow_report_lines');
        Schema::dropIfExists('cash_flow_reports');
        Schema::dropIfExists('balance_sheet_lines');
        Schema::dropIfExists('balance_sheets');
        Schema::dropIfExists('trial_balances');
        Schema::dropIfExists('income_statement_lines');
        Schema::dropIfExists('income_statements');
        Schema::dropIfExists('financial_reports');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->string('report_type');
            $table->date('report_period_start');
            $table->date('report_period_end');
            $table->dateTime('generated_at');
            $table->timestamps();
        });

        Schema::create('income_statements', function (Blueprint $table) {
            $table->id('income_statement_id');
            $table->foreignId('report_id')->constrained('financial_reports', 'report_id')->cascadeOnDelete();
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('income_statement_lines', function (Blueprint $table) {
            $table->id('income_statement_line_id');
            $table->foreignId('income_statement_id')->constrained('income_statements', 'income_statement_id')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->date('report_period_start')->nullable();
            $table->date('report_period_end')->nullable();
            $table->string('line_name');
            $table->string('category');
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('line_order')->default(0);
            $table->timestamps();
        });

        Schema::create('trial_balances', function (Blueprint $table) {
            $table->id('trial_balance_id');
            $table->foreignId('report_id')->constrained('financial_reports', 'report_id')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_name');
            $table->decimal('debit_amount', 15, 2)->nullable();
            $table->decimal('credit_amount', 15, 2)->nullable();
            $table->unsignedInteger('line_order')->default(0);
            $table->timestamps();
        });

        Schema::create('balance_sheets', function (Blueprint $table) {
            $table->id('balance_sheet_id');
            $table->foreignId('report_id')->constrained('financial_reports', 'report_id')->cascadeOnDelete();
            $table->string('statement_title')->default('Balance Sheet');
            $table->string('period_label');
            $table->dateTime('generated_at');
            $table->timestamps();
        });

        Schema::create('balance_sheet_lines', function (Blueprint $table) {
            $table->id('balance_sheet_line_id');
            $table->foreignId('balance_sheet_id')->constrained('balance_sheets', 'balance_sheet_id')->cascadeOnDelete();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('line_name');
            $table->enum('section', ['Asset', 'Liability', 'Equity']);
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('line_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cash_flow_reports', function (Blueprint $table) {
            $table->id('cash_flow_id');
            $table->foreignId('report_id')->constrained('financial_reports', 'report_id')->cascadeOnDelete();
            $table->string('statement_title')->default('Cash Flow Statement');
            $table->string('period_label');
            $table->dateTime('generated_at');
            $table->timestamps();
        });

        Schema::create('cash_flow_report_lines', function (Blueprint $table) {
            $table->id('cash_flow_line_id');
            $table->foreignId('cash_flow_id')->constrained('cash_flow_reports', 'cash_flow_id')->cascadeOnDelete();
            $table->string('activity_type', 50);
            $table->string('line_name');
            $table->decimal('amount', 15, 2);
            $table->unsignedInteger('line_order')->default(0);
            $table->timestamps();
        });
    }
};
