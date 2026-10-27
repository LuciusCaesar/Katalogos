<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Immutable fact-log entry: one row per actual governance score change of a
 * business asset. Rows are never updated; the latest row is the current score.
 *
 * @property int $id
 * @property int $business_asset_id
 * @property float $score Ratio of achieved over max possible points (0-1)
 * @property float|null $achieved_score Absolute achieved points
 * @property float $max_possible_score Maximum achievable points
 * @property int $definition_version Version of the criteria definition (GovernanceCriteria::DEFINITION_VERSION)
 * @property int|null $data_initiative_id Snapshot of the asset's data initiative membership at calculation time
 * @property array<string, bool> $criteria_results
 * @property array<string, float> $criteria_weights
 * @property array<string, mixed>|null $changes
 * @property Carbon $calculated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class GovernanceScore extends Model
{
    protected $fillable = [
        'business_asset_id',
        'score',
        'achieved_score',
        'max_possible_score',
        'definition_version',
        'data_initiative_id',
        'criteria_results',
        'criteria_weights',
        'changes',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'decimal:12',
        'achieved_score' => 'decimal:4',
        'max_possible_score' => 'decimal:4',
        'definition_version' => 'integer',
        'data_initiative_id' => 'integer',
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

    /**
     * Get the data initiative this score snapshots membership for.
     * Historical snapshot only — the asset may have been reassigned since.
     *
     * @return BelongsTo<DataInitiative, $this>
     */
    public function dataInitiative(): BelongsTo
    {
        return $this->belongsTo(DataInitiative::class);
    }
}
