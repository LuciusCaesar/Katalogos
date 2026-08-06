<?php

namespace App\Http\Resources;

use App\Models\GovernanceCriterion;
use App\Models\GovernanceScore;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\BusinessAsset
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
            'governance_score_details' => $this->governanceScore ? /** @var GovernanceScore $score */
                (function (GovernanceScore $score) {
                    return [
                        'score' => $score->score,
                        'max_possible' => $score->max_possible_score,
                        'calculated_at' => $score->calculated_at,
                        'criteria' => collect($score->criteria_results)
                            ->map(fn ($result, $key) => [
                                'key' => $key,
                                'met' => $result,
                                'weight' => $score->criteria_weights[$key] ?? 0,
                                'name' => GovernanceCriterion::where('key', $key)->value('name'),
                                'description' => GovernanceCriterion::where('key', $key)->value('description'),
                                'category' => GovernanceCriterion::where('key', $key)->value('category'),
                            ])
                            ->values(),
                    ];
                })($this->governanceScore) : null,
        ];
    }
}
