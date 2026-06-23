<?php

namespace App\Services;

use App\Models\BusinessAsset;
use App\Models\GovernanceCriterion;
use App\Models\GovernanceScore;
use Illuminate\Support\Collection;

class GovernanceScoreService
{
    /**
     * Calculate and save governance score for a business asset.
     *
     * @param  array<string, mixed>|null  $changes  What triggered the recalculation
     */
    public function calculateAndSave(BusinessAsset $businessAsset, ?array $changes = null): GovernanceScore
    {
        // Load all active criteria
        $criteria = GovernanceCriterion::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Calculate criteria results
        /** @var array<string, bool> $results */
        $results = [];
        /** @var array<string, float> $weights */
        $weights = [];
        $totalWeight = 0.0;
        $achievedWeight = 0.0;

        foreach ($criteria as $criterion) {
            $result = $this->evaluateCriterion($businessAsset, $criterion);
            $weight = (float) $criterion->weight;

            $results[$criterion->key] = $result;
            $weights[$criterion->key] = $weight;
            $totalWeight += $weight;

            if ($result) {
                $achievedWeight += $weight;
            }
        }

        $score = $totalWeight > 0 ? $achievedWeight / $totalWeight : 0;

        // Save new score entry (this becomes both current score and history entry)
        return GovernanceScore::create([
            'business_asset_id' => $businessAsset->id,
            'score' => $score,
            'max_possible_score' => $totalWeight,
            'criteria_results' => $results,
            'criteria_weights' => $weights,
            'changes' => $changes,
            'calculated_at' => now(),
        ]);
    }

    /**
     * Evaluate a single criterion against a business asset.
     */
    private function evaluateCriterion(BusinessAsset $businessAsset, GovernanceCriterion $criterion): bool
    {
        return match ($criterion->key) {
            'has_name' => ! empty($businessAsset->name),
            'has_definition' => ! empty($businessAsset->definition),
            'has_domain' => $businessAsset->domain !== null,
            'has_data_initiative' => $businessAsset->dataInitiative !== null,
            'has_business_rule' => $businessAsset->businessRules()->exists(),
            'has_data_source' => $businessAsset->dataSources()->exists(),
            'has_data_steward' => $businessAsset->dataSteward()->exists(),
            'has_data_owner' => $businessAsset->dataOwner()->exists(),
            default => false,
        };
    }

    /**
     * Get current governance score for a business asset.
     */
    public function getScore(BusinessAsset $businessAsset): ?GovernanceScore
    {
        /** @var GovernanceScore|null $score */
        $score = $businessAsset->governanceScores()->latest()->first();

        return $score;
    }

    /**
     * Get score history for a business asset.
     *
     * @return Collection<int, GovernanceScore>
     */
    public function getHistory(BusinessAsset $businessAsset, int $limit = 50): Collection
    {
        return $businessAsset->governanceScores()
            ->orderBy('calculated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Bulk recalculate scores for all business assets.
     */
    public function recalculateAll(): void
    {
        BusinessAsset::query()
            ->with(['domain', 'dataInitiative', 'businessRules', 'dataSources', 'dataSteward', 'dataOwner'])
            ->chunk(100, function (Collection $assets) {
                foreach ($assets as $asset) {
                    $this->calculateAndSave($asset, ['reason' => 'bulk_recalculation']);
                }
            });
    }
}
