<?php

namespace App\Http\Resources;

use App\Models\KPI;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin KPI
*/
class KPIResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'business_objective_id' => $this->business_objective_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
