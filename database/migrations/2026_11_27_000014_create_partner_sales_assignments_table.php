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
        Schema::create('partner_sales_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->unsignedBigInteger('business_partner_id');
            $table->foreignId('sales_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('assignment_type', 20)->default('PRIMARY')->comment('PRIMARY or SECONDARY');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();

            $table->index(['company_id', 'business_partner_id', 'effective_from'], 'ix_psa_company_partner_date');
            $table->index(['company_id', 'sales_user_id', 'effective_to'], 'ix_psa_company_sales_date');

            // Composite FK ensures partner belongs to the same company
            $table->foreign(['company_id', 'business_partner_id'], 'fk_psa_company_partner')
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
        Schema::dropIfExists('partner_sales_assignments');
    }
};
