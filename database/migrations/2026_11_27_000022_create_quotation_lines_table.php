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
        Schema::create('quotation_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_revision_id')
                ->constrained('quotation_revisions')
                ->cascadeOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('charge_code_id')
                ->nullable()
                ->constrained('charge_codes')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('description', 190);
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('unit', 20)->default('UNIT');
            $table->decimal('quantity', 19, 4)->default(1);
            $table->decimal('unit_price', 19, 4)->default(0);
            $table->decimal('sell_amount', 19, 4)->default(0);
            $table->decimal('cost_unit_price', 19, 4)->default(0);
            $table->decimal('cost_amount', 19, 4)->default(0);
            $table->decimal('profit_amount', 19, 4)->default(0);
            $table->string('remarks', 255)->nullable();
            $table->timestamps();

            $table->index('quotation_revision_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_lines');
    }
};
