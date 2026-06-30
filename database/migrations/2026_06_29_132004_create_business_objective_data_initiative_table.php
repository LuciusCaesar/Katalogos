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
        Schema::create('business_objective_data_initiative', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_objective_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_initiative_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['business_objective_id', 'data_initiative_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_objective_data_initiative');
    }
};
