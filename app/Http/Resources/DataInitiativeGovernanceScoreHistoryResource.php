<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\DataInitiativeGovernanceScoreHistory
 */
class DataInitiativeGovernanceScoreHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'data_initiative_id' => $this->data_initiative_id,
            'score' => $this->score,
            'event' => $this->event,
            'calculated_at' => $this->calculated_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
