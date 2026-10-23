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
        Schema::create('business_event_business_process', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_process_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['business_event_id', 'business_process_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_event_business_process');
    }
};
