<?php

namespace App\Listeners;

use App\Events\BusinessAssetChanged;
use App\Models\BusinessAsset;
use App\Services\DataInitiativeGovernanceScoreService;
use App\Services\GovernanceScoreService;

class CalculateGovernanceScore
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private GovernanceScoreService $scoreService,
        private DataInitiativeGovernanceScoreService $initiativeScoreService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(BusinessAssetChanged $event): void
    {
        $asset = $event->businessAsset;

        // 1. Calculate Business Asset governance score
        $this->scoreService->calculateAndSave(
            $asset,
            $event->changes ?? ['reason' => 'model_changed']
        );

        // 2. Trigger Data Initiative recalculation if it has one
        if ($asset->data_initiative_id) {
            $this->initiativeScoreService->calculateAndSave(
                $asset->dataInitiative,
                $this->buildEventMessage($asset, $event->changes)
            );
        }
    }

    /**
     * Build a descriptive event message for Data Initiative history.
     *
     * @param  array<string, mixed>|null  $changes
     */
    private function buildEventMessage(BusinessAsset $asset, ?array $changes): string
    {
        if ($changes === null) {
            return "Business Asset #{$asset->id} changed";
        }

        if (isset($changes['action'])) {
            if ($changes['action'] === 'created') {
                return "Business Asset #{$asset->id} created";
            }
            if ($changes['action'] === 'deleted') {
                return "Business Asset #{$asset->id} deleted";
            }
            if (isset($changes['changed_fields']) && in_array('data_initiative_id', $changes['changed_fields'], true)) {
                return "Business Asset #{$asset->id} reassigned";
            }
            if (isset($changes['changed_fields'])) {
                /** @var array<string> $changedFields */
                $changedFields = $changes['changed_fields'];

                return "Business Asset #{$asset->id} updated: ".implode(', ', $changedFields);
            }
        }

        return "Business Asset #{$asset->id} changed";
    }
}
