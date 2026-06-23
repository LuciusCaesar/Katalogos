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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_initiative_governance_score_history');
    }
};
