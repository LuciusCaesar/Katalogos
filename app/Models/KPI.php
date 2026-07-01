<?php

namespace App\Models;

use Database\Factories\KPIFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KPI extends Model
{
    /** @use HasFactory<KPIFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'business_objective_id'];

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
