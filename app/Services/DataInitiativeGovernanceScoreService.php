<?php

namespace App\Services;

use App\Models\DataInitiative;
use App\Models\DataInitiativeGovernanceScoreHistory;
use Illuminate\Support\Collection;

class DataInitiativeGovernanceScoreService
{
    /**
     * Track recently processed events to prevent duplicates within the same request.
     * Key: initiative_id, Value: array of event hashes
     *
     * @var array<int, array<string>>
     */
    private static array $processedEvents = [];

    /**
     * Calculate and save average governance score for a Data Initiative.
     *
     * @param  string  $event  Description of what triggered the recalculation
     */
    public function calculateAndSave(DataInitiative $dataInitiative, string $event): void
    {
        // Eager load business assets with their latest governance scores
        $businessAssets = $dataInitiative->businessAssets()
            ->with(['governanceScore' => fn ($query) => $query->latest()])
            ->get();

        // Collect all non-null scores
        /** @var Collection<int, float> $scores */
        $scores = $businessAssets
            ->pluck('governanceScore.score')
            ->filter(fn ($score) => $score !== null);

        // Calculate simple average (NOT weighted)
        $average = $scores->count() > 0
            ? $scores->sum() / $scores->count()
            : 0;

        // Only create history entry if score has changed or this is the first entry
        $currentScore = $dataInitiative->average_governance_score;
        $scoreChanged = $currentScore === null || abs($average - $currentScore) > 0.000001;

        // Prevent duplicate processing of the same event for the same initiative within the same request
        $initiativeId = $dataInitiative->id;
        $eventKey = md5($event);

        if (! isset(self::$processedEvents[$initiativeId])) {
            self::$processedEvents[$initiativeId] = [];
        }

        // If score hasn't changed AND we've already processed this event, skip
        if (! $scoreChanged && in_array($eventKey, self::$processedEvents[$initiativeId], true)) {
            return;
        }

        self::$processedEvents[$initiativeId][] = $eventKey;

        // Update denormalized column on DataInitiative
        $dataInitiative->update([
            'average_governance_score' => $average,
        ]);

        // Log to history
        DataInitiativeGovernanceScoreHistory::create([
            'data_initiative_id' => $dataInitiative->id,
            'score' => $average,
            'event' => $event,
            'calculated_at' => now(),
        ]);
    }

    /**
     * Bulk recalculate scores for all Data Initiatives.
     */
    public function recalculateAll(): void
    {
        DataInitiative::query()
            ->with(['businessAssets' => fn ($query) => $query
                ->with(['governanceScore' => fn ($q) => $q->latest()]),
            ])
            ->chunk(100, function (Collection $initiatives) {
                foreach ($initiatives as $initiative) {
                    $this->calculateAndSave($initiative, 'bulk_recalculation');
                }
            });
    }

    /**
     * Clear the static event tracking cache (for testing).
     */
    public static function clearProcessedEvents(): void
    {
        self::$processedEvents = [];
    }
}
