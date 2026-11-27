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
        Schema::create('tax_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('code', 20)->comment('Tax code: VAT0, VAT5, VAT8, VAT10, EXEMPT, etc.');
            $table->string('name', 100);
            $table->decimal('rate', 7, 4)->comment('Tax percentage rate, e.g. 10.0000, 8.0000, 5.0000, 0.0000');
            $table->string('tax_type', 20)->default('VAT')->comment('VAT, WITHHOLDING, EXEMPT, ZERO, OTHER');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code', 'effective_from']);
            $table->index(['company_id', 'is_active', 'effective_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_codes');
    }
};
