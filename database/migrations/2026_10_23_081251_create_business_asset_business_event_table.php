<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The relation_type column anticipates the future distinction between
     * "business asset subject of business event" and "business asset context
     * of business event" relations: it is nullable for now (undifferentiated
     * relation), and typed relation methods will filter on it once needed. At
     * that point the pair unique index should be replaced by a composite index
     * on (business_asset_id, business_event_id, relation_type).
     */
    public function up(): void
    {
        Schema::create('business_asset_business_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_event_id')->constrained()->cascadeOnDelete();
            $table->string('relation_type')->nullable();
            $table->timestamps();

            $table->unique(['business_asset_id', 'business_event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_asset_business_event');
    }
};
