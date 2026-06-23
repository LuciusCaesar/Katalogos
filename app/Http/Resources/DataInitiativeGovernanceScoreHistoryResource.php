<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property float $score
 * @property string $event
 * @property Carbon $calculated_at
 * @property Carbon $created_at
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
            'score' => $this->score,
            'event' => $this->event,
            'calculated_at' => $this->calculated_at,
            'created_at' => $this->created_at,
        ];
    }
}
