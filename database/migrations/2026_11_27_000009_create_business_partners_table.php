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
        Schema::create('business_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('partner_code', 30)->comment('Unique partner code within company');
            $table->string('legal_name', 200)->comment('Registered business or individual legal name');
            $table->string('display_name', 200)->nullable()->comment('Trade or commonly used brand name');
            $table->string('partner_kind', 20)->default('ORGANIZATION')->comment('ORGANIZATION or INDIVIDUAL');
            $table->string('tax_code', 40)->nullable()->comment('Tax identification number');
            $table->string('registration_no', 60)->nullable()->comment('Business registration / national identity');
            $table->string('website', 255)->nullable();
            $table->foreignId('default_currency_id')
                ->nullable()
                ->constrained('currencies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('payment_term_id')
                ->nullable()
                ->constrained('payment_terms')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('status', 20)->default('ACTIVE')->comment('ACTIVE, INACTIVE, HOLD');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'partner_code']);
            // Supporting composite FK from partner child tables for tenant isolation
            $table->unique(['company_id', 'id']);
            $table->index(['company_id', 'status', 'legal_name']);
            $table->index(['company_id', 'tax_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_partners');
    }
};
