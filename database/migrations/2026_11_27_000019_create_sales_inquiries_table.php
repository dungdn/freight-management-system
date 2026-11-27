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
        Schema::create('sales_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('inquiry_number', 40);
            $table->foreignId('business_partner_id')
                ->constrained('business_partners')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('service_type_id')
                ->nullable()
                ->constrained('service_types')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('direction', 10)->comment('OUTBOUND, INBOUND, DOMESTIC');
            $table->string('transport_mode', 20)->comment('OCEAN, AIR, ROAD, RAIL');
            $table->foreignId('origin_location_id')
                ->nullable()
                ->constrained('locations')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('destination_location_id')
                ->nullable()
                ->constrained('locations')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('status', 20)->default('OPEN')->comment('OPEN, QUOTED, WON, LOST, CANCELLED');
            $table->foreignId('owner_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->date('requested_ship_date')->nullable();
            $table->text('cargo_summary')->nullable();
            $table->decimal('est_gross_weight_kg', 19, 4)->nullable();
            $table->decimal('est_volume_cbm', 19, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'inquiry_number'], 'uq_inquiry_company_number');
            $table->index(['company_id', 'branch_id', 'status'], 'idx_inquiry_branch_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_inquiries');
    }
};
