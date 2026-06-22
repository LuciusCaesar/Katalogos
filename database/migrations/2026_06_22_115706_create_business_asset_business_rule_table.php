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
        Schema::create('business_asset_business_rule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_rule_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['business_asset_id', 'business_rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_asset_business_rule');
    }
};
