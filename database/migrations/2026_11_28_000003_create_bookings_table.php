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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('booking_number', 40);
            $table->foreignId('carrier_partner_id')
                ->nullable()
                ->constrained('business_partners')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->string('booking_reference', 100)->nullable()->comment('Carrier confirmation reference');
            $table->string('status', 20)->default('REQUESTED')->comment('REQUESTED, CONFIRMED, CANCELLED, REJECTED');
            $table->dateTime('confirmed_at')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();

            $table->unique(['company_id', 'booking_number'], 'uq_booking_company_number');
            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
