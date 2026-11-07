<?php

namespace App\Models;

use Database\Factories\BusinessObjectiveFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class BusinessObjective extends Model
{
    /** @use HasFactory<BusinessObjectiveFactory> */
    use HasFactory, Searchable;

    protected $fillable = ['name', 'description'];

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
     * Get all data initiatives associated with this business objective.
     *
     * @return BelongsToMany<DataInitiative, $this>
     */
    public function dataInitiatives(): BelongsToMany
    {
        return $this->belongsToMany(DataInitiative::class, 'business_objective_data_initiative');
    }

    /**
     * Get all KPIs associated with this business objective.
     *
     * @return HasMany<KPI, $this>
     */
    public function kpis(): HasMany
    {
        return $this->hasMany(KPI::class);
    }
}
