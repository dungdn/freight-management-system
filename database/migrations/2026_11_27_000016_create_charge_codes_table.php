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
        Schema::create('charge_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('charge_category_id')
                ->constrained('charge_categories')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('code', 30)->comment('Charge code: OFR, THC, DOC, CIC, CFS, SEAL, etc.');
            $table->string('name', 120);
            $table->string('default_unit', 20)->nullable()->comment('Default pricing basis: CONT, CBM, KGS, SET, BL, etc.');
            $table->foreignId('tax_code_id')
                ->nullable()
                ->constrained('tax_codes')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'charge_category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charge_codes');
    }
};
