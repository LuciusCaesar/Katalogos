<?php

namespace App\Models;

use Database\Factories\DataInitiativeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DataInitiative extends Model
{
    /** @use HasFactory<DataInitiativeFactory> */
    use HasFactory;

    protected $fillable = ['code', 'label', 'description', 'average_governance_score'];

    protected $casts = [
        'average_governance_score' => 'decimal:8',
    ];

    /**
     * Get all business assets belonging to this data initiative.
     *
     * @return HasMany<BusinessAsset, $this>
     */
    public function businessAssets(): HasMany
    {
        return $this->hasMany(BusinessAsset::class);
    }

    /**
     * Get all role assignments for this data initiative.
     *
     * @return MorphMany<RoleAssignment, $this>
     */
    public function roleAssignments(): MorphMany
    {
        return $this->morphMany(RoleAssignment::class, 'roleable');
    }

    /**
     * Get all users assigned to this data initiative with any role.
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
     * Get all roles assigned to this data initiative.
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
     * Get the Data Steward for this data initiative.
     *
     * @return BelongsToMany<User, $this, RoleAssignment>
     */
    public function dataSteward(): BelongsToMany
    {
        return $this->users()->wherePivot('role_id', function ($query) {
            $query->select('id')->from('roles')->where('name', 'Data Steward');
        });
    }

    /**
     * Get the Data Owner for this data initiative.
     *
     * @return BelongsToMany<User, $this, RoleAssignment>
     */
    public function dataOwner(): BelongsToMany
    {
        return $this->users()->wherePivot('role_id', function ($query) {
            $query->select('id')->from('roles')->where('name', 'Data Owner');
        });
    }

    /**
     * Assign a role to a user for this data initiative.
     */
    public function assignRoleToUser(User $user, Role $role): RoleAssignment
    {
        return $this->roleAssignments()->create([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    /**
     * Remove a role assignment from a user for this data initiative.
     */
    public function removeRoleFromUser(User $user, Role $role): bool
    {
        return $this->roleAssignments()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->delete() > 0;
    }

    /**
     * Get all governance score history entries for this data initiative.
     *
     * @return HasMany<DataInitiativeGovernanceScoreHistory, $this>
     */
    public function governanceScoreHistory(): HasMany
    {
        return $this->hasMany(DataInitiativeGovernanceScoreHistory::class)
            ->orderBy('calculated_at', 'desc');
    }
}
