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
        Schema::create('data_issue_root_cause', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('root_cause_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['data_issue_id', 'root_cause_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_issue_root_cause');
    }
};
