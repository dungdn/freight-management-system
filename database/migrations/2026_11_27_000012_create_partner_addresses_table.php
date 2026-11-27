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
        Schema::create('partner_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->unsignedBigInteger('business_partner_id');
            $table->string('address_type', 30)->default('OFFICE')->comment('REGISTERED, BILLING, OFFICE, WAREHOUSE, PICKUP, DELIVERY, OTHER');
            $table->string('label', 100)->nullable();
            $table->string('contact_name', 160)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('address_line1', 200);
            $table->string('address_line2', 200)->nullable();
            $table->string('ward', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->foreignId('country_id')
                ->constrained('countries')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['business_partner_id', 'address_type', 'is_active'], 'ix_paddr_partner_type_active');
            $table->index(['company_id', 'country_id', 'city'], 'ix_paddr_company_country_city');

            // Composite FK ensures partner belongs to the same company
            $table->foreign(['company_id', 'business_partner_id'], 'fk_paddr_company_partner')
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
        Schema::dropIfExists('partner_addresses');
    }
};
