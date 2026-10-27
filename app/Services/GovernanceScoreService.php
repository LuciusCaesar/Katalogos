<?php

namespace App\Services;

use App\Models\BusinessAsset;
use App\Models\GovernanceScore;
use Illuminate\Support\Collection;

class GovernanceScoreService
{
    /**
     * Tolerance used when comparing two scores for equality.
     */
    private const FLOAT_EPSILON = 1e-9;

    /**
     * Pure calculation: evaluate the code-defined criteria against the asset.
     *
     * @return array{score: float, achieved_score: float, max_possible_score: float, criteria_results: array<string, bool>, criteria_weights: array<string, float>}
     */
    public function calculate(BusinessAsset $businessAsset): array
    {
        /** @var array<string, bool> $results */
        $results = [];
        /** @var array<string, float> $weights */
        $weights = [];
        $maxPossible = 0.0;
        $achieved = 0.0;

        foreach (GovernanceCriteria::all() as $criterion) {
            $met = (bool) $criterion['evaluate']($businessAsset);
            $weight = (float) $criterion['weight'];

            $results[$criterion['key']] = $met;
            $weights[$criterion['key']] = $weight;
            $maxPossible += $weight;

            if ($met) {
                $achieved += $weight;
            }
        }

        return [
            'score' => $maxPossible > 0 ? $achieved / $maxPossible : 0.0,
            'achieved_score' => $achieved,
            'max_possible_score' => $maxPossible,
            'criteria_results' => $results,
            'criteria_weights' => $weights,
        ];
    }

    /**
     * Calculate the governance score of a business asset and append a row to
     * the fact log — but only if the score actually changed since the latest
     * row. This makes the operation idempotent: replaying the same change
     * (e.g. a double form submission) never duplicates history entries.
     *
     * @param  array<string, mixed>|null  $changes  What triggered the recalculation
     */
    public function calculateAndSave(BusinessAsset $businessAsset, ?array $changes = null): ?GovernanceScore
    {
        $calculation = $this->calculate($businessAsset);

        $latest = GovernanceScore::query()
            ->where('business_asset_id', $businessAsset->id)
            ->orderByDesc('calculated_at')
            ->orderByDesc('id')
            ->first();

        if ($latest !== null && $this->representsSameState($latest, $businessAsset, $calculation)) {
            return null;
        }

        return GovernanceScore::create([
            'business_asset_id' => $businessAsset->id,
            'score' => $calculation['score'],
            'achieved_score' => $calculation['achieved_score'],
            'max_possible_score' => $calculation['max_possible_score'],
            'definition_version' => GovernanceCriteria::DEFINITION_VERSION,
            'data_initiative_id' => $businessAsset->data_initiative_id,
            'criteria_results' => $calculation['criteria_results'],
            'criteria_weights' => $calculation['criteria_weights'],
            'changes' => $changes,
            'calculated_at' => now(),
        ]);
    }

    /**
     * Whether a logged row already represents the current state of the asset:
     * same criteria definition version, same achieved points, same criteria
     * results and same data initiative membership snapshot.
     *
     * @param  array{score: float, achieved_score: float, max_possible_score: float, criteria_results: array<string, bool>, criteria_weights: array<string, float>}  $calculation
     */
    private function representsSameState(GovernanceScore $latest, BusinessAsset $businessAsset, array $calculation): bool
    {
        if ($latest->definition_version !== GovernanceCriteria::DEFINITION_VERSION) {
            return false;
        }

        if ($latest->data_initiative_id !== $businessAsset->data_initiative_id) {
            return false;
        }

        if (abs((float) $latest->achieved_score - $calculation['achieved_score']) > self::FLOAT_EPSILON) {
            return false;
        }

        if (($latest->criteria_results ?? []) != $calculation['criteria_results']) {
            return false;
        }

        foreach ($calculation['criteria_weights'] as $key => $weight) {
            if (abs((float) ($latest->criteria_weights[$key] ?? 0) - $weight) > self::FLOAT_EPSILON) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get current governance score for a business asset.
     */
    public function getScore(BusinessAsset $businessAsset): ?GovernanceScore
    {
        /** @var GovernanceScore|null $score */
        $score = $businessAsset->governanceScores()
            ->orderByDesc('calculated_at')
            ->orderByDesc('id')
            ->first();

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
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Bulk recalculate scores for all business assets.
     * Appends a row only for assets whose score actually changed.
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
