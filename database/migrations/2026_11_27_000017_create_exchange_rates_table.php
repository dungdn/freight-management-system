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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('from_currency_id')
                ->constrained('currencies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('to_currency_id')
                ->constrained('currencies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('rate_type', 20)->default('ACCOUNTING')->comment('BUY, SELL, ACCOUNTING, SPOT, CUSTOMER');
            $table->decimal('rate', 20, 10)->comment('Exchange conversion multiplier: to_amount = from_amount * rate');
            $table->dateTime('effective_at');
            $table->string('source', 80)->nullable()->comment('Source bank or authority, e.g. VCB, STATE_BANK');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();

            $table->unique(
                ['company_id', 'from_currency_id', 'to_currency_id', 'rate_type', 'effective_at'],
                'uq_exchange_rates_scope'
            );
            $table->index(
                ['company_id', 'from_currency_id', 'to_currency_id', 'effective_at'],
                'ix_exchange_rates_lookup'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
