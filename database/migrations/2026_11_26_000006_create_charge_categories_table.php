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
        Schema::create('charge_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique()->comment('Category code: FREIGHT, ORIGIN_LOCAL, DEST_LOCAL, CUSTOMS, INLAND, etc.');
            $table->string('name', 100);
            $table->string('financial_side', 10)->comment('REVENUE, COST, or BOTH');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charge_categories');
    }
};
