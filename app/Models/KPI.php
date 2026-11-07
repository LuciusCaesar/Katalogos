<?php

namespace App\Models;

use Database\Factories\KPIFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class KPI extends Model
{
    /** @use HasFactory<KPIFactory> */
    use HasFactory, Searchable;

    protected $fillable = ['name', 'description', 'business_objective_id'];

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
     * Get the business objective that this KPI belongs to.
     *
     * @return BelongsTo<BusinessObjective, $this>
     */
    public function businessObjective(): BelongsTo
    {
        return $this->belongsTo(BusinessObjective::class);
    }
}
