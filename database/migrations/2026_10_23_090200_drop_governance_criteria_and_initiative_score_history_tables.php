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
        // Governance criteria are now defined in code (App\Services\GovernanceCriteria).
        Schema::dropIfExists('data_initiative_governance_score_history');
        Schema::dropIfExists('governance_criteria');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('governance_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('weight', 10, 4)->default(1.0);
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('data_initiative_governance_score_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_initiative_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 10, 8);
            $table->string('event');
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index('data_initiative_id');
            $table->index('calculated_at');
        });
    }
};
