<?php

namespace App\Models;

use Database\Factories\DataInitiativeGovernanceScoreHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataInitiativeGovernanceScoreHistory extends Model
{
    /** @use HasFactory<DataInitiativeGovernanceScoreHistoryFactory> */
    use HasFactory;

    protected $table = 'data_initiative_governance_score_history';

    protected $fillable = [
        'data_initiative_id',
        'score',
        'event',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'decimal:8',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get the data initiative this history entry belongs to.
     *
     * @return BelongsTo<DataInitiative, $this>
     */
    public function dataInitiative(): BelongsTo
    {
        return $this->belongsTo(DataInitiative::class);
    }
}
