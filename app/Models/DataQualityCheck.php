<?php

namespace App\Models;

use Database\Factories\DataQualityCheckFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     */
    public function businessRule(): BelongsTo
    {
        return $this->belongsTo(BusinessRule::class);
    }

    /**
     * Get all data sources associated with this data quality check.
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
}
