<?php

namespace App\Models;

use Database\Factories\DataSourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DataSource extends Model
{
    /** @use HasFactory<DataSourceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all business assets associated with this data source.
     *
     * @return BelongsToMany<BusinessAsset, $this>
     */
    public function businessAssets(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'business_asset_data_source',
            'data_source_id',
            'business_asset_id'
        );
    }
}
