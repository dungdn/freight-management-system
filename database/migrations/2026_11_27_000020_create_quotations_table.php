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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('quotation_number', 40);
            $table->foreignId('inquiry_id')
                ->nullable()
                ->constrained('sales_inquiries')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('business_partner_id')
                ->constrained('business_partners')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('service_type_id')
                ->nullable()
                ->constrained('service_types')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('direction', 10)->nullable()->comment('OUTBOUND, INBOUND, DOMESTIC');
            $table->string('transport_mode', 20)->nullable()->comment('OCEAN, AIR, ROAD, RAIL');
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
            $table->string('status', 20)->default('DRAFT')->comment('DRAFT, SENT, ACCEPTED, REJECTED, EXPIRED');
            $table->unsignedSmallInteger('current_revision_number')->default(1);
            $table->date('valid_until')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();

            $table->unique(['company_id', 'quotation_number'], 'uq_quotation_company_number');
            $table->index(['company_id', 'business_partner_id', 'status'], 'idx_quotation_partner_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
