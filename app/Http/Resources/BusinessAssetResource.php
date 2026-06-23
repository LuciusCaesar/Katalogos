<?php

namespace App\Http\Resources;

use App\Models\GovernanceCriterion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'governance_score' => $this->governanceScore?->score,
            'governance_score_details' => $this->governanceScore ? [
                'score' => $this->governanceScore->score,
                'max_possible' => $this->governanceScore->max_possible_score,
                'calculated_at' => $this->governanceScore->calculated_at,
                'criteria' => collect($this->governanceScore->criteria_results)
                    ->map(fn ($result, $key) => [
                        'key' => $key,
                        'met' => $result,
                        'weight' => $this->governanceScore->criteria_weights[$key] ?? 0,
                        'name' => GovernanceCriterion::where('key', $key)->value('name'),
                        'description' => GovernanceCriterion::where('key', $key)->value('description'),
                        'category' => GovernanceCriterion::where('key', $key)->value('category'),
                    ])
                    ->values(),
            ] : null,
        ];
    }
}
