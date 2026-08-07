<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Database\Factories\RegulationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $documentation_url
 */
class Regulation extends Model
{
    /** @use HasFactory<RegulationFactory> */
    use Filterable, HasFactory, Searchable;

    protected $fillable = [
        'name',
        'description',
        'documentation_url',
    ];

    /**
     * Get fields that should be searched for Regulations.
     *
     * @return array<string>
     */
    public function getSearchableFields(): array
    {
        return ['name', 'description'];
    }

    /**
     * Get all business assets associated with this regulation.
     *
     * @return BelongsToMany<BusinessAsset, $this>
     */
    public function businessAssets(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'business_asset_regulation',
            'regulation_id',
            'business_asset_id'
        );
    }
}
