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
        Schema::create('quotation_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')
                ->constrained('quotations')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->unsignedSmallInteger('revision_number')->default(1);
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->char('currency_code', 3)->nullable();
            $table->decimal('exchange_rate', 19, 8)->nullable()->default(1.00000000);
            $table->decimal('total_sell_amount', 19, 4)->default(0);
            $table->decimal('total_cost_amount', 19, 4)->default(0);
            $table->decimal('est_gross_profit', 19, 4)->default(0);
            $table->text('terms_and_conditions')->nullable();
            $table->json('terms_snapshot')->nullable();
            $table->string('revision_status', 20)->default('DRAFT')->comment('DRAFT, ACTIVE, SUPERSEDED, ACCEPTED, REJECTED');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();

            $table->unique(['quotation_id', 'revision_number'], 'uq_quotation_revision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_revisions');
    }
};
