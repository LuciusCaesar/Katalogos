<?php

namespace App\Services;

use App\Models\DataInitiative;
use App\Models\GovernanceScore;
use Illuminate\Support\Carbon;

/**
 * Read-only projection of data initiative governance metrics.
 *
 * Initiative-level governance is never written to the database: it is
 * computed on read by replaying the business asset score fact log
 * (governance_scores). Each score row snapshots the asset's data initiative
 * membership, so the chronological sweep can rebuild both the current
 * metrics and the full evolution timeline of an initiative — including
 * assets that moved from one initiative to another.
 */
class DataInitiativeGovernanceProjection
{
    /**
     * Tolerance used when comparing aggregate values for equality.
     */
    private const FLOAT_EPSILON = 1e-9;

    /**
     * Current governance metrics of an initiative, computed from the latest
     * score of each of its member assets.
     *
     * @return array{average: float|null, total_points: float, max_possible_points: float, scored_assets: int, total_assets: int}
     */
    public function metrics(DataInitiative $dataInitiative): array
    {
        $assets = $dataInitiative->businessAssets()
            ->with('governanceScore')
            ->get();

        $sumRatio = 0.0;
        $sumAchieved = 0.0;
        $sumMax = 0.0;
        $scoredAssets = 0;

        foreach ($assets as $asset) {
            $score = $asset->governanceScore;

            if ($score === null) {
                continue;
            }

            $max = (float) $score->max_possible_score;
            $achieved = (float) $score->achieved_score;

            $sumRatio += $max > 0 ? $achieved / $max : 0.0;
            $sumAchieved += $achieved;
            $sumMax += $max;
            $scoredAssets++;
        }

        return [
            'average' => $scoredAssets > 0 ? $sumRatio / $scoredAssets : null,
            'total_points' => $sumAchieved,
            'max_possible_points' => $sumMax,
            'scored_assets' => $scoredAssets,
            'total_assets' => $assets->count(),
        ];
    }

    /**
     * Rebuild the evolution timeline of an initiative from the score fact log.
     *
     * Every score row of every asset is replayed in chronological order; each
     * row carries a snapshot of the asset's initiative membership, so the
     * running aggregate always reflects exactly the assets that belonged to
     * the initiative at that moment. A timeline point is emitted only when
     * the aggregate actually changed.
     *
     * @return array<int, array{calculated_at: Carbon, average: float|null, total_points: float, max_possible_points: float, scored_assets: int, business_asset_id: int, business_asset: string|null, event: string}>
     */
    public function history(DataInitiative $dataInitiative, int $limit = 100): array
    {
        $initiativeId = $dataInitiative->id;

        /** @var array<int, array{initiative_id: int|null, ratio: float, achieved: float, max: float}> $currentPerAsset */
        $currentPerAsset = [];
        $sumRatio = 0.0;
        $sumAchieved = 0.0;
        $sumMax = 0.0;
        $count = 0;

        /** @var array<int, array{calculated_at: Carbon, average: float|null, total_points: float, max_possible_points: float, scored_assets: int, business_asset_id: int, business_asset: string|null, event: string}> $points */
        $points = [];

        // Aggregate state as of the last emitted point (initial = empty initiative)
        $lastAverage = null;
        $lastPoints = 0.0;
        $lastCount = 0;

        $rows = GovernanceScore::query()
            ->orderBy('calculated_at')
            ->orderBy('id')
            ->with('businessAsset:id,name')
            ->cursor();

        foreach ($rows as $row) {
            $assetId = $row->business_asset_id;
            $previous = $currentPerAsset[$assetId] ?? null;

            // The asset leaves its previous initiative: remove its contribution
            if ($previous !== null && $previous['initiative_id'] === $initiativeId) {
                $sumRatio -= $previous['ratio'];
                $sumAchieved -= $previous['achieved'];
                $sumMax -= $previous['max'];
                $count--;
            }

            $max = (float) $row->max_possible_score;
            $achieved = (float) $row->achieved_score;
            $currentPerAsset[$assetId] = [
                'initiative_id' => $row->data_initiative_id,
                'ratio' => $max > 0 ? $achieved / $max : 0.0,
                'achieved' => $achieved,
                'max' => $max,
            ];

            // The asset joins this initiative: add its contribution
            if ($row->data_initiative_id === $initiativeId) {
                $sumRatio += $max > 0 ? $achieved / $max : 0.0;
                $sumAchieved += $achieved;
                $sumMax += $max;
                $count++;
            }

            $average = $count > 0 ? round($sumRatio / $count, 10) : null;
            $totalPoints = round($sumAchieved, 4);

            // Emit a point only when the aggregate changed
            $averageChanged = $average === null
                ? $lastAverage !== null
                : $lastAverage === null || abs($average - $lastAverage) > self::FLOAT_EPSILON;

            if (! $averageChanged
                && abs($totalPoints - $lastPoints) <= self::FLOAT_EPSILON
                && $count === $lastCount) {
                continue;
            }

            $lastAverage = $average;
            $lastPoints = $totalPoints;
            $lastCount = $count;

            $points[] = [
                'calculated_at' => $row->calculated_at,
                'average' => $average,
                'total_points' => $totalPoints,
                'max_possible_points' => round($sumMax, 4),
                'scored_assets' => $count,
                'business_asset_id' => $assetId,
                'business_asset' => $row->businessAsset?->name,
                'event' => $this->describe($row),
            ];
        }

        // Most recent first
        return array_slice(array_reverse($points), 0, $limit);
    }

    /**
     * Build a human-readable description of the event that produced a score row.
     */
    private function describe(GovernanceScore $row): string
    {
        $label = $row->businessAsset !== null
            ? '#'.$row->business_asset_id.' "'.$row->businessAsset->name.'"'
            : '#'.$row->business_asset_id;

        $changes = $row->changes;

        if (! is_array($changes)) {
            return "Business Asset {$label} recalculated";
        }

        /** @var mixed $action */
        $action = $changes['action'] ?? null;

        return match ($action) {
            'created' => "Business Asset {$label} created",
            'restored' => "Business Asset {$label} restored",
            'updated' => isset($changes['changed_fields']) && is_array($changes['changed_fields'])
                ? "Business Asset {$label} updated: ".implode(', ', $changes['changed_fields'])
                : "Business Asset {$label} updated",
            default => "Business Asset {$label} ".(is_string($action) ? $action : 'recalculated'),
        };
    }
}
