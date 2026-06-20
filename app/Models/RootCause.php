<?php

namespace App\Models;

use App\Enums\RootCauseDimension;
use Database\Factories\RootCauseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RootCause extends Model
{
    /** @use HasFactory<RootCauseFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'dimension',
    ];

    protected $casts = [
        'dimension' => RootCauseDimension::class,
    ];

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
}
