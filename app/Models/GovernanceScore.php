<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function businessAsset(): BelongsTo
    {
        return $this->belongsTo(BusinessAsset::class);
    }
}
