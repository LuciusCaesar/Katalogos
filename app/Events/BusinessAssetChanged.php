<?php

namespace App\Events;

use App\Models\BusinessAsset;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusinessAssetChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  array|null  $changes  What triggered this change
     */
    public function __construct(
        public BusinessAsset $businessAsset,
        public ?array $changes = null
    ) {}
}
