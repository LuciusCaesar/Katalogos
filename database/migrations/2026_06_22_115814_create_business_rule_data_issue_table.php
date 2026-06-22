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
        Schema::create('business_rule_data_issue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_issue_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['business_rule_id', 'data_issue_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_rule_data_issue');
    }
};
