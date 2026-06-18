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
        Schema::table('role_assignments', function (Blueprint $table) {
            // Add unique constraint to ensure only one user can have a specific role per entity
            $table->unique(['role_id', 'roleable_id', 'roleable_type'], 'role_assignments_role_per_entity_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_assignments', function (Blueprint $table) {
            $table->dropUnique('role_assignments_role_per_entity_unique');
        });
    }
};
