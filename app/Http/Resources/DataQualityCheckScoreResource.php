<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataQualityCheckScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data_quality_check_id' => $this->data_quality_check_id,
            'rows_passed' => $this->rows_passed,
            'rows_failed' => $this->rows_failed,
            'total_rows' => $this->total_rows,
            'score' => $this->score,
            'score_percentage' => $this->score_percentage,
            'origin_type' => $this->origin_type,
            'origin_id' => $this->origin_id,
            'origin_name' => $this->origin_name,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
