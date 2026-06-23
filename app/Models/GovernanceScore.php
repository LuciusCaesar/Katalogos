<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $business_asset_id
 * @property float $score
 * @property float $max_possible_score
 * @property array<string, bool> $criteria_results
 * @property array<string, float> $criteria_weights
 * @property array<string, mixed> $changes
 * @property Carbon $calculated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class GovernanceScore extends Model
{
    protected $fillable = [
        'business_asset_id',
        'score',
        'max_possible_score',
        'criteria_results',
        'criteria_weights',
        'changes',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'decimal:8',
        'max_possible_score' => 'decimal:4',
        'criteria_results' => 'array',
        'criteria_weights' => 'array',
        'changes' => 'array',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get the business asset this score belongs to.
     *
     * @return BelongsTo<BusinessAsset, $this>
     */
    public function businessAsset(): BelongsTo
    {
        return $this->belongsTo(BusinessAsset::class);
    }
}
