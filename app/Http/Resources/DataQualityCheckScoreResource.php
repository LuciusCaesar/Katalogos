<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $data_quality_check_id
 * @property int $rows_passed
 * @property int $rows_failed
 * @property int $total_rows
 * @property float $score
 * @property string $score_percentage
 * @property string|null $origin_type
 * @property int|null $origin_id
 * @property string|null $origin_name
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
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
