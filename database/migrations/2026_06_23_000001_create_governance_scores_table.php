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
        Schema::create('governance_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_asset_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 10, 8);
            $table->decimal('max_possible_score', 10, 4);
            $table->json('criteria_results');
            $table->json('criteria_weights');
            $table->json('changes')->nullable();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index('business_asset_id');
            $table->index('calculated_at');
            $table->index('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('governance_scores');
    }
};
