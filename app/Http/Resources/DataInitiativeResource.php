<?php

namespace App\Http\Resources;

use App\Models\DataInitiative;
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
        return [
            'id' => $this->id,
            'code' => $this->code,
            'label' => $this->label,
            'description' => $this->description,
            'average_governance_score' => $this->average_governance_score,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
