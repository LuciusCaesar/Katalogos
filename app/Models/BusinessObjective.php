<?php

namespace App\Models;

use Database\Factories\BusinessObjectiveFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BusinessObjective extends Model
{
    /** @use HasFactory<BusinessObjectiveFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Get all data initiatives associated with this business objective.
     *
     * @return BelongsToMany<DataInitiative, $this>
     */
    public function dataInitiatives(): BelongsToMany
    {
        return $this->belongsToMany(DataInitiative::class, 'business_objective_data_initiative');
    }
}
