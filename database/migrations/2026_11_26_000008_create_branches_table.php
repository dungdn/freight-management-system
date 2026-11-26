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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('code', 20)->comment('Branch office code');
            $table->string('name', 160)->comment('Branch name, e.g. Ho Chi Minh HQ, Hai Phong Port Office');
            $table->string('timezone', 64)->nullable()->comment('IANA timezone if different from company');
            $table->boolean('is_head_office')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
            // Supporting composite FK from child tables (departments, access, etc.) for cross-company isolation
            $table->unique(['company_id', 'id']);
            $table->index(['company_id', 'is_active', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
