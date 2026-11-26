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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Unique company business code');
            $table->string('legal_name', 200)->comment('Registered legal business name');
            $table->string('display_name', 200)->nullable()->comment('Trading/display brand name');
            $table->string('tax_code', 40)->nullable()->comment('Tax identification number');
            $table->foreignId('base_currency_id')
                ->constrained('currencies')
                ->restrictOnDelete()
                ->restrictOnUpdate()
                ->comment('Company primary accounting currency');
            $table->string('timezone', 64)->default('Asia/Ho_Chi_Minh')->comment('IANA timezone');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
