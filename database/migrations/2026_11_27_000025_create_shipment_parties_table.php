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
        Schema::create('shipment_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->string('party_role', 30)->comment('SHIPPER, CONSIGNEE, NOTIFY_PARTY, CARRIER, AGENT, CUSTOMS_BROKER, FORWARDER');
            $table->foreignId('business_partner_id')
                ->nullable()
                ->constrained('business_partners')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('name_snapshot', 190);
            $table->text('address_snapshot')->nullable();
            $table->string('contact_snapshot', 190)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['shipment_id', 'party_role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_parties');
    }
};
