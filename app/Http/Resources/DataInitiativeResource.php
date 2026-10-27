<?php

namespace App\Http\Resources;

use App\Models\DataInitiative;
use App\Services\DataInitiativeGovernanceProjection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DataInitiative
 */
class DataInitiativeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // Governance metrics are computed on read from the asset score log
        $metrics = app(DataInitiativeGovernanceProjection::class)->metrics($this->resource);

        return [
            'id' => $this->id,
            'code' => $this->code,
            'label' => $this->label,
            'description' => $this->description,
            'average_governance_score' => $metrics['average'],
            'total_governance_score' => $metrics['total_points'],
            'max_governance_score' => $metrics['max_possible_points'],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
