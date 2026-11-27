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
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                ->constrained('countries')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->char('iata_code', 3)->nullable()->unique()->comment('IATA 3-letter code, e.g. SGN, HAN, SIN');
            $table->char('icao_code', 4)->nullable()->unique()->comment('ICAO 4-letter code, e.g. VVTS, VVNB, WSSS');
            $table->string('name', 160)->comment('Airport name, e.g. Tan Son Nhat, Noi Bai');
            $table->string('timezone', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
