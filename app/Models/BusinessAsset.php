<?php

namespace App\Models;

use Database\Factories\BusinessAssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<DataInitiative, $this>
     */
    public function dataInitiative()
    {
        return $this->belongsTo(DataInitiative::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<Domain, $this>
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get all role assignments for this business asset.
     *
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\MorphMany<RoleAssignment, $this>
     */
    public function roleAssignments()
    {
        return $this->morphMany(RoleAssignment::class, 'roleable');
    }

    /**
     * Get all users assigned to this business asset with any role.
     *
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, $this, RoleAssignment>
     */
    public function users()
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
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Role, $this, RoleAssignment>
     */
    public function roles()
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
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, $this, RoleAssignment>
     */
    public function dataSteward()
    {
        return $this->users()->whereHas(
            'roles',
            fn ($query) => $query->where('name', 'Data Steward')
        );
    }

    /**
     * Get the Data Owner for this business asset.
     *
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, $this, RoleAssignment>
     */
    public function dataOwner()
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
