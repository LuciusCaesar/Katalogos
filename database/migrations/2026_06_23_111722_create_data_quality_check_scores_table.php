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
        Schema::create('data_quality_check_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_quality_check_id')->constrained()->cascadeOnDelete();
            $table->integer('rows_passed');
            $table->integer('rows_failed');
            $table->integer('total_rows');
            $table->decimal('score', 5, 4);
            $table->string('origin_type')->default('user');
            $table->foreignId('origin_id')->nullable()->constrained('users');
            $table->string('origin_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['data_quality_check_id', 'created_at']);
            $table->index('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_quality_check_scores');
    }
};
