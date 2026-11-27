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
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique()->comment('Service code: OCEAN_FCL_EXPORT, OCEAN_FCL_IMPORT, AIR_EXPORT, etc.');
            $table->string('name', 100);
            $table->foreignId('transport_mode_id')
                ->nullable()
                ->constrained('transport_modes')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['transport_mode_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
