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
        Schema::create('shipment_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->unsignedSmallInteger('leg_sequence')->default(1);
            $table->string('transport_mode', 20)->comment('OCEAN, AIR, ROAD, RAIL');
            $table->foreignId('origin_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('destination_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->dateTime('planned_departure')->nullable();
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('planned_arrival')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            $table->foreignId('carrier_partner_id')
                ->nullable()
                ->constrained('business_partners')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('vessel_name', 100)->nullable();
            $table->string('voyage_no', 50)->nullable();
            $table->timestamps();

            $table->unique(['shipment_id', 'leg_sequence'], 'uq_shipment_leg_sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_legs');
    }
};
