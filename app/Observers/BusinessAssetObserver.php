<?php

namespace App\Observers;

use App\Events\BusinessAssetChanged;
use App\Models\BusinessAsset;

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
        event(new BusinessAssetChanged($businessAsset, $changes));
    }

    /**
     * Handle the BusinessAsset "deleted" event.
     * We skip score calculation on delete since scores are cascade-deleted.
     */
    public function deleted(BusinessAsset $businessAsset): void
    {
        // Skip score calculation - scores will be automatically cascade-deleted
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
