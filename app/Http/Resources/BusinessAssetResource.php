<?php

namespace App\Http\Resources;

use App\Models\BusinessAsset;
use App\Models\GovernanceScore;
use App\Services\GovernanceCriteria;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin BusinessAsset
 */
class BusinessAssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'definition' => $this->definition,
            'data_initiative_id' => $this->data_initiative_id,
            'domain_id' => $this->domain_id,
            'consistency' => $this->consistency,
            'integrity' => $this->integrity,
            'availability' => $this->availability,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'governance_score' => $this->governanceScore?->score,
            'governance_score_details' => $this->governanceScore !== null
                ? $this->governanceScoreDetails($this->governanceScore)
                : null,
        ];
    }

    /**
     * @return array{score: float, achieved_score: float|null, max_possible: float, calculated_at: Carbon, criteria: array<int, array{key: string, met: bool, weight: float, name: string|null, description: string|null, category: string|null}>}
     */
    private function governanceScoreDetails(GovernanceScore $score): array
    {
        $criteria = [];

        foreach ($score->criteria_results as $key => $met) {
            $criterion = GovernanceCriteria::find($key);

            $criteria[] = [
                'key' => $key,
                'met' => $met,
                'weight' => $score->criteria_weights[$key] ?? 0,
                'name' => $criterion['name'] ?? null,
                'description' => $criterion['description'] ?? null,
                'category' => $criterion['category'] ?? null,
            ];
        }

        return [
            'score' => $score->score,
            'achieved_score' => $score->achieved_score,
            'max_possible' => $score->max_possible_score,
            'calculated_at' => $score->calculated_at,
            'criteria' => $criteria,
        ];
    }
}
