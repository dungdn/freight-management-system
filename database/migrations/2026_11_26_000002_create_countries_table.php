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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->char('iso2', 2)->unique()->comment('ISO 3166-1 alpha-2 code, e.g. VN, US, CN');
            $table->char('iso3', 3)->unique()->comment('ISO 3166-1 alpha-3 code, e.g. VNM, USA, CHN');
            $table->string('name', 120)->index();
            $table->string('phone_code', 8)->nullable()->comment('International dialing prefix, e.g. +84');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
