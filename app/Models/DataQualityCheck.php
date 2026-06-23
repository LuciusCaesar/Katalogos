<?php

namespace App\Models;

use Database\Factories\DataQualityCheckFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DataQualityCheck extends Model
{
    /** @use HasFactory<DataQualityCheckFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'business_rule_id',
    ];

    /**
     * Get the business rule that owns this data quality check.
     *
     * @return BelongsTo<BusinessRule, $this>
     */
    public function businessRule(): BelongsTo
    {
        return $this->belongsTo(BusinessRule::class);
    }

    /**
     * Get all data sources associated with this data quality check.
     *
     * @return BelongsToMany<DataSource, $this>
     */
    public function dataSources(): BelongsToMany
    {
        return $this->belongsToMany(
            DataSource::class,
            'data_quality_check_data_source',
            'data_quality_check_id',
            'data_source_id'
        );
    }

    /**
     * Get all scores for this data quality check.
     *
     * @return HasMany<DataQualityCheckScore, $this>
     */
    public function scores(): HasMany
    {
        return $this->hasMany(DataQualityCheckScore::class);
    }

    /**
     * Get the latest score for this data quality check.
     *
     * @return HasOne<DataQualityCheckScore, $this>
     */
    public function latestScore(): HasOne
    {
        return $this->hasOne(DataQualityCheckScore::class)->latestOfMany();
    }
}
