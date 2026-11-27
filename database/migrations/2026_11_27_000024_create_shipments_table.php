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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('shipment_number', 40);
            $table->foreignId('sales_order_id')
                ->nullable()
                ->constrained('sales_orders')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('quotation_id')
                ->nullable()
                ->constrained('quotations')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('business_partners')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('direction', 10)->default('OUTBOUND')->comment('OUTBOUND, INBOUND, DOMESTIC');
            $table->string('transport_mode', 20)->default('OCEAN')->comment('OCEAN, AIR, ROAD, RAIL');
            $table->foreignId('service_type_id')
                ->nullable()
                ->constrained('service_types')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('status', 20)->default('DRAFT')->comment('DRAFT, READY, ACTIVE, COMPLETED, CLOSED, CANCELLED');
            $table->string('operational_status', 30)->nullable()->comment('BOOKING_CONFIRMED, CARGO_RECEIVED, DEPARTED, IN_TRANSIT, ARRIVED, CUSTOMS_CLEARED, DELIVERED');
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
            $table->string('vessel_name', 100)->nullable();
            $table->string('voyage_no', 50)->nullable();
            $table->string('master_bl_no', 50)->nullable();
            $table->string('house_bl_no', 50)->nullable();
            $table->foreignId('carrier_partner_id')
                ->nullable()
                ->constrained('business_partners')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->dateTime('etd')->nullable();
            $table->dateTime('eta')->nullable();
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();

            $table->unique(['company_id', 'shipment_number'], 'uq_shipment_company_number');
            $table->index(['company_id', 'branch_id', 'status'], 'idx_shipment_company_branch_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
