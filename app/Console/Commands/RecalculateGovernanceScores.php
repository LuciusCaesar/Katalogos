<?php

namespace App\Console\Commands;

use App\Models\BusinessAsset;
use App\Services\DataInitiativeGovernanceScoreService;
use App\Services\GovernanceScoreService;
use Illuminate\Console\Command;

class RecalculateGovernanceScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'governance:recalculate
        {--asset= : Recalculate for a specific business asset ID}
        {--all : Recalculate for all business assets}
        {--force : Force recalculation even if score exists}
        {--initiatives : Also recalculate Data Initiative governance scores}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate governance scores for business assets and optionally Data Initiatives';

    /**
     * Execute the console command.
     */
    public function handle(
        GovernanceScoreService $scoreService,
        DataInitiativeGovernanceScoreService $initiativeScoreService
    ): int {
        if ($this->option('asset')) {
            $asset = BusinessAsset::findOrFail($this->option('asset'));
            $this->info("Recalculating governance score for Business Asset #{$asset->id}...");
            $scoreService->calculateAndSave($asset, ['reason' => 'manual_recalculation']);
            $freshAsset = $asset->fresh();
            $score = $freshAsset->governanceScore?->score;
            $this->info("Score: {$score}");

            // Recalculate Data Initiative if asset has one and --initiatives flag is set
            if ($this->option('initiatives') && $asset->data_initiative_id) {
                $this->info('Recalculating Data Initiative governance score...');
                $initiativeScoreService->calculateAndSave(
                    $asset->dataInitiative,
                    'manual_recalculation'
                );
            }

            return 0;
        }

        if ($this->option('all')) {
            $this->info('Recalculating governance scores for all business assets...');
            $scoreService->recalculateAll();
            $this->info('Business Asset scores recalculated.');

            // Always recalculate Data Initiative scores when using --all
            $this->info('Recalculating governance scores for all Data Initiatives...');
            $initiativeScoreService->recalculateAll();
            $this->info('Data Initiative scores recalculated.');

            $this->info('All scores recalculated.');

            return 0;
        }

        $this->error('Please specify --asset or --all option.');

        return 1;
    }
}
