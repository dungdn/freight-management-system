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
        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->string('event_code', 40)->comment('BOOKING_CONFIRMED, CARGO_RECEIVED, CUSTOMS_CLEARED, DEPARTED, ARRIVED, DELIVERED, EXCEPTION');
            $table->dateTime('planned_at')->nullable();
            $table->dateTime('actual_at')->nullable();
            $table->string('event_source', 30)->nullable()->comment('MANUAL, EDI, AIS_VESSEL, GPS, API');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('corrected_event_id')->nullable();
            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();

            $table->index(['shipment_id', 'actual_at', 'planned_at'], 'idx_shipment_events_timeline');
            $table->index(['shipment_id', 'event_code'], 'idx_shipment_events_code');
            $table->foreign('corrected_event_id')
                ->references('id')
                ->on('shipment_events')
                ->nullOnDelete()
                ->restrictOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
    }
};
