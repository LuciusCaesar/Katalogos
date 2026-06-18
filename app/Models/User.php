<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\BusinessAsset;
use App\Models\DataInitiative;
use App\Models\Role;
use App\Models\RoleAssignment;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get all role assignments for this user.
     */
    public function roleAssignments()
    {
        return $this->hasMany(RoleAssignment::class);
    }

    /**
     * Get all roles assigned to this user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_assignments')
            ->withPivot('roleable_id', 'roleable_type')
            ->using(RoleAssignment::class);
    }

    /**
     * Get all DataInitiatives where this user has a role.
     */
    public function dataInitiatives()
    {
        return $this->belongsToMany(
            DataInitiative::class,
            'role_assignments',
            'user_id',
            'roleable_id'
        )->withPivot('role_id', 'roleable_type')
            ->wherePivot('roleable_type', DataInitiative::class)
            ->using(RoleAssignment::class);
    }

    /**
     * Get all BusinessAssets where this user has a role.
     */
    public function businessAssets()
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'role_assignments',
            'user_id',
            'roleable_id'
        )->withPivot('role_id', 'roleable_type')
            ->wherePivot('roleable_type', BusinessAsset::class)
            ->using(RoleAssignment::class);
    }

    /**
     * Check if the user has a specific role on a specific entity.
     */
    public function hasRoleOn(string $roleName, $entity): bool
    {
        if (! $entity instanceof Model) {
            return false;
        }

        return $this->roleAssignments()
            ->where('roleable_id', $entity->id)
            ->where('roleable_type', $entity::class)
            ->whereHas('role', fn ($query) => $query->where('name', $roleName))
            ->exists();
    }

    /**
     * Check if the user is a Data Steward for the given entity.
     */
    public function isDataStewardFor($entity): bool
    {
        return $this->hasRoleOn('Data Steward', $entity);
    }

    /**
     * Check if the user is a Data Owner for the given entity.
     */
    public function isDataOwnerFor($entity): bool
    {
        return $this->hasRoleOn('Data Owner', $entity);
    }
}
