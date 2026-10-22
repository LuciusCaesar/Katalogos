<?php

namespace App\Models;

use App\Enums\RootCauseDimension;
use Database\Factories\RootCauseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class RootCause extends Model
{
    /** @use HasFactory<RootCauseFactory> */
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'description',
        'dimension',
    ];

    protected $casts = [
        'dimension' => RootCauseDimension::class,
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
     * Get all data issues associated with this root cause.
     *
     * @return BelongsToMany<DataIssue, $this>
     */
    public function dataIssues(): BelongsToMany
    {
        return $this->belongsToMany(
            DataIssue::class,
            'data_issue_root_cause',
            'root_cause_id',
            'data_issue_id'
        );
    }

    /**
     * Get all solutions associated with this root cause.
     *
     * @return BelongsToMany<Solution, $this>
     */
    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(
            Solution::class,
            'root_cause_solution',
            'root_cause_id',
            'solution_id'
        );
    }
}
