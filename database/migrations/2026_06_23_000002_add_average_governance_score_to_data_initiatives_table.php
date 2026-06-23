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
        Schema::table('data_initiatives', function (Blueprint $table) {
            $table->decimal('average_governance_score', 10, 8)->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_initiatives', function (Blueprint $table) {
            $table->dropColumn('average_governance_score');
        });
    }
};
