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
        Schema::create('container_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Container type: 20GP, 40GP, 40HC, 45HC, 20RF, 40RF, 20OT, 40OT, etc.');
            $table->string('name', 80);
            $table->string('iso_size_type_code', 10)->nullable()->comment('ISO 6346 code, e.g. 22G1, 42G1, 45G1');
            $table->decimal('nominal_length_ft', 5, 2)->nullable()->comment('Nominal length in feet: 20, 40, 45');
            $table->boolean('is_reefer')->default(false);
            $table->boolean('is_open_top')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_types');
    }
};
