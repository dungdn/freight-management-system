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
        Schema::create('shipment_cargo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('package_type_id')
                ->nullable()
                ->constrained('package_types')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('package_type_code', 30)->nullable();
            $table->decimal('package_count', 19, 4)->default(1);
            $table->decimal('gross_weight_kg', 19, 4)->nullable();
            $table->decimal('volume_cbm', 19, 4)->nullable();
            $table->foreignId('container_type_id')
                ->nullable()
                ->constrained('container_types')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('container_number', 30)->nullable();
            $table->string('seal_number', 30)->nullable();
            $table->text('description')->nullable();
            $table->text('marks_and_numbers')->nullable();
            $table->timestamps();

            $table->index('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_cargo_items');
    }
};
