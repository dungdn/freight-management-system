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
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                ->constrained('countries')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->char('unlocode', 5)->unique()->comment('UN/LOCODE, e.g. VNSGN, VNHPH, SGSIN, USLAX');
            $table->string('name', 160)->comment('Port name, e.g. Cat Lai, Hai Phong Port');
            $table->string('port_type', 20)->nullable()->comment('SEAPORT, RIVER_PORT, INLAND_PORT, etc.');
            $table->string('timezone', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
