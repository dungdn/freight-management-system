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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('sales_order_number', 40);
            $table->foreignId('quotation_id')
                ->constrained('quotations')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('quotation_revision_id')
                ->nullable()
                ->constrained('quotation_revisions')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('business_partner_id')
                ->constrained('business_partners')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('status', 30)->default('ACCEPTED')->comment('ACCEPTED, HANDOFF_PENDING, CONVERTED_TO_SHIPMENT, CANCELLED');
            $table->decimal('total_amount', 19, 4)->default(0);
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('converted_at')->nullable();
            $table->text('operational_instructions')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();

            $table->unique(['company_id', 'sales_order_number'], 'uq_sales_order_company_number');
            $table->index(['company_id', 'branch_id', 'status'], 'idx_sales_order_branch_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
