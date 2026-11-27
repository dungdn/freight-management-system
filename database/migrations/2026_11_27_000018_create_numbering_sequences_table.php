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
        Schema::create('numbering_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->unsignedBigInteger('branch_scope_id')->default(0)->comment('0 = Company-wide; non-zero = Branch-specific');
            $table->string('document_type', 40)->comment('INQUIRY, QUOTATION, SALES_ORDER, SHIPMENT, BOOKING, HBL, MBL, etc.');
            $table->string('period_key', 10)->default('ALL')->comment('Year period e.g. 2026, or ALL');
            $table->string('prefix', 30)->default('')->comment('Document prefix e.g. SGN-EXP-, HPH-DO-');
            $table->unsignedBigInteger('next_number')->default(1)->comment('Next sequential integer to allocate');
            $table->unsignedSmallInteger('padding')->default(6)->comment('Zero-padding length, e.g. 6 -> 000001');
            $table->string('reset_policy', 20)->default('YEARLY')->comment('YEARLY, MONTHLY, NEVER');
            $table->timestamps();

            $table->unique(
                ['company_id', 'branch_scope_id', 'document_type', 'period_key'],
                'uq_num_seq_scope'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numbering_sequences');
    }
};
