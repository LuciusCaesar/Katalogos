<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataInitiative extends Model
{
    /** @use HasFactory<\Database\Factories\DataInitiativeFactory> */
    use HasFactory;

    protected $fillable = ['code', 'label', 'description'];

    public function businessAssets()
    {
        return $this->hasMany(BusinessAsset::class);
    }

    /**
     * Get all role assignments for this data initiative.
     */
    public function roleAssignments()
    {
        return $this->morphMany(RoleAssignment::class, 'roleable');
    }

    /**
     * Get all users assigned to this data initiative with any role.
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
     * Get all roles assigned to this data initiative.
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
     * Get the Data Steward for this data initiative.
     */
    public function dataSteward()
    {
        return $this->users()->whereHas(
            'roles',
            fn ($query) => $query->where('name', 'Data Steward')
        );
    }

    /**
     * Get the Data Owner for this data initiative.
     */
    public function dataOwner()
    {
        return $this->users()->whereHas(
            'roles',
            fn ($query) => $query->where('name', 'Data Owner')
        );
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
}
