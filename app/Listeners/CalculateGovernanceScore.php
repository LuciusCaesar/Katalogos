<?php

namespace App\Listeners;

use App\Events\BusinessAssetChanged;
use App\Services\GovernanceScoreService;

class CalculateGovernanceScore
{
    /**
     * Create the event listener.
     */
    public function __construct(private GovernanceScoreService $scoreService) {}

    /**
     * Handle the event.
     */
    public function handle(BusinessAssetChanged $event): void
    {
        $this->scoreService->calculateAndSave(
            $event->businessAsset,
            $event->changes ?? ['reason' => 'model_changed']
        );
    }
}
