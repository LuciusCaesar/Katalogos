<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('governance_scores', function (Blueprint $table) {
            // Absolute achieved points (sum of achieved criteria weights)
            $table->decimal('achieved_score', 10, 4)->nullable()->after('score');
            // Version of the criteria definition used for this calculation
            $table->unsignedInteger('definition_version')->default(1)->after('max_possible_score');
            // Snapshot of the asset's data initiative membership at calculation
            // time. Deliberately NOT a foreign key: it is a historical snapshot
            // used to rebuild initiative-level metrics from the score log.
            $table->unsignedBigInteger('data_initiative_id')->nullable()->after('definition_version');

            $table->index('data_initiative_id');
        });

        // Backfill existing rows from the stored ratio
        DB::statement('UPDATE governance_scores SET achieved_score = score * max_possible_score');
        DB::statement('UPDATE governance_scores SET data_initiative_id = (SELECT data_initiative_id FROM business_assets WHERE business_assets.id = governance_scores.business_asset_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('governance_scores', function (Blueprint $table) {
            $table->dropIndex(['data_initiative_id']);
            $table->dropColumn(['achieved_score', 'definition_version', 'data_initiative_id']);
        });
    }
};
