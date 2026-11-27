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
        Schema::create('partner_credit_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->unsignedBigInteger('business_partner_id');
            $table->decimal('credit_limit', 19, 4)->nullable()->comment('Commercial credit limit amount');
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->unsignedSmallInteger('credit_days')->default(0)->comment('Allowed payment credit days');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'business_partner_id', 'effective_from', 'effective_to'], 'ix_pcp_company_partner_dates');

            // Composite FK ensures partner belongs to the same company
            $table->foreign(['company_id', 'business_partner_id'], 'fk_pcp_company_partner')
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
        Schema::dropIfExists('partner_credit_profiles');
    }
};
