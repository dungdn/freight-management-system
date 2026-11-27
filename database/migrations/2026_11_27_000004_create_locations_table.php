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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                ->constrained('countries')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->unsignedBigInteger('parent_location_id')->nullable();
            $table->string('location_type', 20)->comment('COUNTRY_REGION, REGION, CITY, DISTRICT, OTHER');
            $table->string('code', 30)->nullable();
            $table->string('name', 160);
            $table->string('timezone', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['country_id', 'location_type', 'code']);
            $table->index(['country_id', 'location_type', 'name']);
            $table->foreign('parent_location_id')
                ->references('id')
                ->on('locations')
                ->restrictOnDelete()
                ->restrictOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
