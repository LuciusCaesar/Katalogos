<?php

namespace App\Models;

use App\Enums\SolutionDimension;
use Database\Factories\SolutionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Solution extends Model
{
    /** @use HasFactory<SolutionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'dimension',
    ];

    protected $casts = [
        'dimension' => SolutionDimension::class,
    ];

    /**
     * Get all root causes associated with this solution.
     *
     * @return BelongsToMany<RootCause, $this>
     */
    public function rootCauses(): BelongsToMany
    {
        return $this->belongsToMany(
            RootCause::class,
            'root_cause_solution',
            'solution_id',
            'root_cause_id'
        );
    }
}
