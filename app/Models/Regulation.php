<?php

namespace App\Models;

use App\Traits\Filterable;
use Database\Factories\RegulationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

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
     * Get the indexable data array for the model.
     *
     * @return array<string, string|null>
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
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
