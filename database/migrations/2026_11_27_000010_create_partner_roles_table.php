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
        Schema::create('partner_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->unsignedBigInteger('business_partner_id');
            $table->string('role_code', 30)->comment('CUSTOMER, VENDOR, AGENT, COLOADER, CARRIER, SHIPPER, CONSIGNEE, etc.');
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();

            $table->unique(['business_partner_id', 'role_code']);
            $table->index(['company_id', 'role_code', 'is_active']);

            // Composite FK ensures partner belongs to the same company
            $table->foreign(['company_id', 'business_partner_id'])
                ->references(['company_id', 'id'])
                ->on('business_partners')
                ->restrictOnDelete()
                ->restrictOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_roles');
    }
};
