<?php

namespace App\Models;

use App\Services\GovernanceScoreService;
use Database\Factories\BusinessAssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string|null $definition
 * @property int|null $data_initiative_id
 * @property int|null $domain_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class BusinessAsset extends Model
{
    /** @use HasFactory<BusinessAssetFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'definition',
        'data_initiative_id',
        'domain_id',
    ];

    protected $casts = [
        'data_initiative_id' => 'integer',
        'domain_id' => 'integer',
    ];

    /**
     * Get the data initiative this asset belongs to.
     *
     * @return BelongsTo<DataInitiative, $this>
     */
    public function dataInitiative(): BelongsTo
    {
        return $this->belongsTo(DataInitiative::class);
    }

    /**
     * Get the domain this asset belongs to.
     *
     * @return BelongsTo<Domain, $this>
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get all role assignments for this business asset.
     *
     * @return MorphMany<RoleAssignment, $this>
     */
    public function roleAssignments(): MorphMany
    {
        return $this->morphMany(RoleAssignment::class, 'roleable');
    }

    /**
     * Get all users assigned to this business asset with any role.
     *
     * @return BelongsToMany<User, $this, RoleAssignment>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'role_assignments',
            'roleable_id',
            'user_id'
        )->withPivot('role_id', 'roleable_type')
            ->wherePivot('roleable_type', self::class)
            ->using(RoleAssignment::class);
    }

    /**
     * Get all roles assigned to this business asset.
     *
     * @return BelongsToMany<Role, $this, RoleAssignment>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_assignments',
            'roleable_id',
            'role_id'
        )->withPivot('user_id', 'roleable_type')
            ->wherePivot('roleable_type', self::class)
            ->using(RoleAssignment::class);
    }

    /**
     * Get the Data Steward for this business asset.
     *
     * @return BelongsToMany<User, $this, RoleAssignment>
     */
    public function dataSteward(): BelongsToMany
    {
        return $this->users()->whereHas(
            'roles',
            fn ($query) => $query->where('name', 'Data Steward')
        );
    }

    /**
     * Get the Data Owner for this business asset.
     *
     * @return BelongsToMany<User, $this, RoleAssignment>
     */
    public function dataOwner(): BelongsToMany
    {
        return $this->users()->whereHas(
            'roles',
            fn ($query) => $query->where('name', 'Data Owner')
        );
    }

    /**
     * Assign a role to a user for this business asset.
     */
    public function assignRoleToUser(User $user, Role $role): RoleAssignment
    {
        return $this->roleAssignments()->create([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    /**
     * Get all data issues associated with this business asset.
     *
     * @return BelongsToMany<DataIssue, $this>
     */
    public function dataIssues(): BelongsToMany
    {
        return $this->belongsToMany(
            DataIssue::class,
            'business_asset_data_issue',
            'business_asset_id',
            'data_issue_id'
        );
    }

    /**
     * Get all data sources associated with this business asset.
     *
     * @return BelongsToMany<DataSource, $this>
     */
    public function dataSources(): BelongsToMany
    {
        return $this->belongsToMany(
            DataSource::class,
            'business_asset_data_source',
            'business_asset_id',
            'data_source_id'
        );
    }

    /**
     * Get all business rules associated with this business asset.
     *
     * @return BelongsToMany<BusinessRule, $this>
     */
    public function businessRules(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessRule::class,
            'business_asset_business_rule',
            'business_asset_id',
            'business_rule_id'
        );
    }

    /**
     * Remove a role assignment from a user for this business asset.
     */
    public function removeRoleFromUser(User $user, Role $role): bool
    {
        return $this->roleAssignments()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->delete() > 0;
    }

    /**
     * Get all governance scores for this business asset (includes history).
     *
     * @return HasMany<GovernanceScore, $this>
     */
    public function governanceScores(): HasMany
    {
        return $this->hasMany(GovernanceScore::class)->orderBy('calculated_at', 'desc');
    }

    /**
     * Get the current governance score for this business asset.
     * Uses latestOfMany() to get the most recent score entry.
     *
     * @return HasOne<GovernanceScore, $this>
     */
    public function governanceScore(): HasOne
    {
        return $this->hasOne(GovernanceScore::class)->latestOfMany();
    }

    /**
     * Calculate and save governance score.
     *
     * @param  array<string, mixed>|null  $changes
     */
    public function calculateGovernanceScore(?array $changes = null): GovernanceScore
    {
        return app(GovernanceScoreService::class)->calculateAndSave($this, $changes);
    }

    /**
     * Get the minimum current data quality check score from all business rules.
     */
    public function getMinDataQualityCheckScoreAttribute(): ?float
    {
        $scores = $this->getAllDataQualityCheckScores();

        if ($scores->isEmpty()) {
            return null;
        }

        return $scores->min();
    }

    /**
     * Get the maximum current data quality check score from all business rules.
     */
    public function getMaxDataQualityCheckScoreAttribute(): ?float
    {
        $scores = $this->getAllDataQualityCheckScores();

        if ($scores->isEmpty()) {
            return null;
        }

        return $scores->max();
    }

    /**
     * Get the average current data quality check score from all business rules.
     */
    public function getAvgDataQualityCheckScoreAttribute(): ?float
    {
        $scores = $this->getAllDataQualityCheckScores();

        if ($scores->isEmpty()) {
            return null;
        }

        return $scores->avg();
    }

    /**
     * Get all current data quality check scores from all business rules.
     *
     * @return Collection<int, float>
     */
    private function getAllDataQualityCheckScores(): Collection
    {
        return $this->businessRules
            ->flatMap(fn (BusinessRule $businessRule) => $businessRule->dataQualityChecks)
            ->pluck('latestScore.score')
            ->filter(fn ($score) => $score !== null);
    }
}
