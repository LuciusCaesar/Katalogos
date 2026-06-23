<?php

namespace App\Observers;

use App\Events\BusinessAssetChanged;
use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Services\DataInitiativeGovernanceScoreService;

class BusinessAssetObserver
{
    /**
     * Handle the BusinessAsset "created" event.
     */
    public function created(BusinessAsset $businessAsset): void
    {
        event(new BusinessAssetChanged($businessAsset, ['action' => 'created']));
    }

    /**
     * Handle the BusinessAsset "updated" event.
     */
    public function updated(BusinessAsset $businessAsset): void
    {
        $changes = ['action' => 'updated', 'changed_fields' => array_keys($businessAsset->getChanges())];

        // If data_initiative_id changed, recalculate BOTH old and new Data Initiative scores
        if (in_array('data_initiative_id', $changes['changed_fields'], true)) {
            $oldId = $businessAsset->getOriginal('data_initiative_id');
            $newId = $businessAsset->data_initiative_id;

            // Recalculate old Data Initiative (if it exists)
            if ($oldId) {
                $oldInitiative = DataInitiative::find($oldId);
                if ($oldInitiative instanceof DataInitiative) {
                    app(DataInitiativeGovernanceScoreService::class)->calculateAndSave(
                        $oldInitiative,
                        "Business Asset #{$businessAsset->id} unlinked"
                    );
                }
            }

            // Recalculate new Data Initiative (if it exists)
            if ($newId) {
                $newInitiative = DataInitiative::find($newId);
                if ($newInitiative instanceof DataInitiative) {
                    app(DataInitiativeGovernanceScoreService::class)->calculateAndSave(
                        $newInitiative,
                        "Business Asset #{$businessAsset->id} linked"
                    );
                }
            }
        }

        event(new BusinessAssetChanged($businessAsset, $changes));
    }

    /**
     * Handle the BusinessAsset "deleted" event.
     * We skip BusinessAsset score calculation on delete since scores are cascade-deleted,
     * but we need to recalculate Data Initiative score before the asset is gone.
     */
    public function deleted(BusinessAsset $businessAsset): void
    {
        // Recalculate Data Initiative before cascade delete removes the asset
        if ($businessAsset->data_initiative_id) {
            $initiative = DataInitiative::find($businessAsset->data_initiative_id);
            if ($initiative instanceof DataInitiative) {
                app(DataInitiativeGovernanceScoreService::class)->calculateAndSave(
                    $initiative,
                    "Business Asset #{$businessAsset->id} deleted"
                );
            }
        }

        // Skip further BusinessAsset score calculation - asset is being deleted
    }

    /**
     * Handle the BusinessAsset "restored" event.
     */
    public function restored(BusinessAsset $businessAsset): void
    {
        event(new BusinessAssetChanged($businessAsset, ['action' => 'restored']));
    }

    /**
     * Handle the BusinessAsset "forceDeleted" event.
     */
    public function forceDeleted(BusinessAsset $businessAsset): void
    {
        // Skip score calculation on force delete
    }
}
