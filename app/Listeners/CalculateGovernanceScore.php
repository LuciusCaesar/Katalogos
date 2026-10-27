<?php

namespace App\Listeners;

use App\Events\BusinessAssetChanged;
use App\Services\GovernanceScoreService;

class CalculateGovernanceScore
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private GovernanceScoreService $scoreService
    ) {}

    /**
     * Handle the event.
     *
     * Appends a score row to the asset's fact log — but only when the score
     * actually changed, which makes replayed events (double submissions)
     * harmless. Initiative-level metrics need no write here: they are
     * projected on read from this log (DataInitiativeGovernanceProjection).
     */
    public function handle(BusinessAssetChanged $event): void
    {
        $this->scoreService->calculateAndSave(
            $event->businessAsset,
            $event->changes ?? ['reason' => 'model_changed']
        );
    }
}
