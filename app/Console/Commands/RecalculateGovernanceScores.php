<?php

namespace App\Console\Commands;

use App\Models\BusinessAsset;
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
        {--force : Force recalculation even if score exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate governance scores for business assets';

    /**
     * Execute the console command.
     */
    public function handle(GovernanceScoreService $scoreService): int
    {
        if ($this->option('asset')) {
            $asset = BusinessAsset::findOrFail($this->option('asset'));
            $this->info("Recalculating governance score for Business Asset #{$asset->id}...");
            $scoreService->calculateAndSave($asset, ['reason' => 'manual_recalculation']);
            $this->info("Score: {$asset->fresh()->governanceScore?->score}");

            return 0;
        }

        if ($this->option('all')) {
            $this->info('Recalculating governance scores for all business assets...');
            $scoreService->recalculateAll();
            $this->info('All scores recalculated.');

            return 0;
        }

        $this->error('Please specify --asset or --all option.');

        return 1;
    }
}
