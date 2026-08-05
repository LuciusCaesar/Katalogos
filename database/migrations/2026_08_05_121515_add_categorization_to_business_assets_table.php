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
        Schema::table('business_assets', function (Blueprint $table) {
            $table->enum('consistency', ['low', 'middle', 'high'])->nullable()->after('domain_id');
            $table->enum('integrity', ['low', 'middle', 'high'])->nullable()->after('consistency');
            $table->enum('availability', ['low', 'middle', 'high'])->nullable()->after('integrity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_assets', function (Blueprint $table) {
            $table->dropColumn(['consistency', 'integrity', 'availability']);
        });
    }
};
