<?php

namespace App\Providers;

use App\Events\BusinessAssetChanged;
use App\Listeners\CalculateGovernanceScore;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        BusinessAssetChanged::class => [
            CalculateGovernanceScore::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
