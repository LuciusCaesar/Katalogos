<?php

namespace App\Models;

use Database\Factories\DataQualityCheckScoreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataQualityCheckScore extends Model
{
    /** @use HasFactory<DataQualityCheckScoreFactory> */
    use HasFactory;

    protected $fillable = [
        'data_quality_check_id',
        'rows_passed',
        'rows_failed',
        'total_rows',
        'score',
        'origin_type',
        'origin_id',
        'origin_name',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:4',
        'rows_passed' => 'integer',
        'rows_failed' => 'integer',
        'total_rows' => 'integer',
    ];

    public function dataQualityCheck(): BelongsTo
    {
        return $this->belongsTo(DataQualityCheck::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'origin_id');
    }

    public function getScorePercentageAttribute(): string
    {
        return number_format($this->score * 100, 2).'%';
    }
}
