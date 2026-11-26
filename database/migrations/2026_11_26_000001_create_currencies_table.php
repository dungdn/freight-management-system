<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->char('code', 3)->unique()->comment('ISO 4217 currency code, e.g. USD, VND, EUR');
            $table->string('name', 80);
            $table->unsignedTinyInteger('minor_unit')->default(2)->comment('Decimal digits, e.g. 0 for VND/JPY, 2 for USD/EUR');
            $table->string('symbol', 8)->nullable()->comment('Symbol, e.g. $, ₫, €');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
