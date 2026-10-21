<?php

namespace App\Models;

use Database\Factories\DomainFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Domain extends Model
{
    /** @use HasFactory<DomainFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Get all business assets belonging to this domain.
     *
     * @return HasMany<BusinessAsset, $this>
     */
    public function businessAssets(): HasMany
    {
        return $this->hasMany(BusinessAsset::class);
    }

    /**
     * Get all role assignments for this domain.
     *
     * @return MorphMany<RoleAssignment, $this>
     */
    public function roleAssignments(): MorphMany
    {
        return $this->morphMany(RoleAssignment::class, 'roleable');
    }

    /**
     * Get all users assigned to this domain with any role.
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
     * Get all roles assigned to this domain.
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
     * Get the Domain Owner for this domain.
     *
     * @return BelongsToMany<User, $this, RoleAssignment>
     */
    public function domainOwner(): BelongsToMany
    {
        return $this->users()->wherePivot('role_id', function ($query) {
            $query->select('id')->from('roles')->where('name', 'Domain Owner');
        });
    }

    /**
     * Assign the Domain Owner role to a user for this domain.
     */
    public function assignDomainOwner(User $user): RoleAssignment
    {
        $role = Role::where('name', 'Domain Owner')->firstOrFail();

        return $this->roleAssignments()->create([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    /**
     * Remove the Domain Owner role assignment from this domain.
     */
    public function removeDomainOwner(): bool
    {
        $role = Role::where('name', 'Domain Owner')->firstOrFail();

        return $this->roleAssignments()
            ->where('role_id', $role->id)
            ->delete() > 0;
    }
}
