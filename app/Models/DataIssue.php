<?php

namespace App\Models;

use Database\Factories\DataIssueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DataIssue extends Model
{
    /** @use HasFactory<DataIssueFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all business assets associated with this data issue.
     *
     * @return BelongsToMany<BusinessAsset, $this, business_asset_data_issue>
     */
    public function businessAssets(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'business_asset_data_issue',
            'data_issue_id',
            'business_asset_id'
        );
    }

    /**
     * Get all root causes associated with this data issue.
     *
     * @return BelongsToMany<RootCause, $this, data_issue_root_cause>
     */
    public function rootCauses(): BelongsToMany
    {
        return $this->belongsToMany(
            RootCause::class,
            'data_issue_root_cause',
            'data_issue_id',
            'root_cause_id'
        );
    }
}
