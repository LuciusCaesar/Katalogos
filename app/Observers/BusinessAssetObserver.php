<?php

namespace App\Observers;

use App\Events\BusinessAssetChanged;
use App\Models\BusinessAsset;

class BusinessAssetObserver
{
    /**
     * Asset fields that can affect the governance score. Updates touching
     * only other fields (e.g. categorization) do not trigger a recalculation.
     */
    private const GOVERNANCE_RELEVANT_FIELDS = [
        'name',
        'definition',
        'domain_id',
        'data_initiative_id',
    ];

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
        $changedFields = array_values(array_intersect(
            array_keys($businessAsset->getChanges()),
            self::GOVERNANCE_RELEVANT_FIELDS
        ));

        if ($changedFields === []) {
            return;
        }

        event(new BusinessAssetChanged($businessAsset, [
            'action' => 'updated',
            'changed_fields' => $changedFields,
        ]));
    }

    /**
     * Handle the BusinessAsset "restored" event.
     */
    public function restored(BusinessAsset $businessAsset): void
    {
        event(new BusinessAssetChanged($businessAsset, ['action' => 'restored']));
    }
}
