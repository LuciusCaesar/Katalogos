<?php

namespace App\Models;

use Database\Factories\BusinessAssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
     * Remove a role assignment from a user for this business asset.
     */
    public function removeRoleFromUser(User $user, Role $role): bool
    {
        return $this->roleAssignments()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->delete() > 0;
    }
}
