<?php

namespace App\Models;

use Database\Factories\BusinessRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessRule extends Model
{
    /** @use HasFactory<BusinessRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all business assets associated with this business rule.
     *
     * @return BelongsToMany<BusinessAsset, $this>
     */
    public function businessAssets(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'business_asset_business_rule',
            'business_rule_id',
            'business_asset_id'
        );
    }

    /**
     * Get all data issues associated with this business rule.
     *
     * @return BelongsToMany<DataIssue, $this>
     */
    public function dataIssues(): BelongsToMany
    {
        return $this->belongsToMany(
            DataIssue::class,
            'business_rule_data_issue',
            'business_rule_id',
            'data_issue_id'
        );
    }

    /**
     * Get all data quality checks associated with this business rule.
     *
     * @return HasMany<DataQualityCheck, $this>
     */
    public function dataQualityChecks(): HasMany
    {
        return $this->hasMany(DataQualityCheck::class);
    }

    /**
     * Get the minimum current score from all data quality checks.
     */
    public function getMinDataQualityCheckScoreAttribute(): ?float
    {
        if ($this->dataQualityChecks->isEmpty()) {
            return null;
        }

        return $this->dataQualityChecks
            ->pluck('latestScore.score')
            ->filter(fn ($score) => $score !== null)
            ->min();
    }

    /**
     * Get the maximum current score from all data quality checks.
     */
    public function getMaxDataQualityCheckScoreAttribute(): ?float
    {
        if ($this->dataQualityChecks->isEmpty()) {
            return null;
        }

        return $this->dataQualityChecks
            ->pluck('latestScore.score')
            ->filter(fn ($score) => $score !== null)
            ->max();
    }

    /**
     * Get the average current score from all data quality checks.
     */
    public function getAvgDataQualityCheckScoreAttribute(): ?float
    {
        if ($this->dataQualityChecks->isEmpty()) {
            return null;
        }

        $scores = $this->dataQualityChecks
            ->pluck('latestScore.score')
            ->filter(fn ($score) => $score !== null);

        if ($scores->isEmpty()) {
            return null;
        }

        return $scores->avg();
    }
}
