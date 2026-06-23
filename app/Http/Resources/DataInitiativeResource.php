<?php

namespace App\Http\Resources;

use App\Models\DataInitiative;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $label
 * @property string|null $description
 * @property float|null $average_governance_score
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property DataInitiative $resource
 */

/**
 * @property int $id
 * @property string $code
 * @property string $label
 * @property string|null $description
 * @property float|null $average_governance_score
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
