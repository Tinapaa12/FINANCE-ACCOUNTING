<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payment_methods');
    }

    public function down(): void
    {
        Schema::create('payment_methods', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
};
