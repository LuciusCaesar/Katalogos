<?php

namespace App\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 */
class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Get the users that have this role assigned.
     *
     * @return BelongsToMany<User, $this, RoleAssignment>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_assignments')
            ->withPivot('roleable_id', 'roleable_type')
            ->using(RoleAssignment::class);
    }

    /**
     * Get the data initiatives where this role is assigned.
     *
     * @return BelongsToMany<DataInitiative, $this, RoleAssignment>
     */
    public function dataInitiatives(): BelongsToMany
    {
        return $this->belongsToMany(
            DataInitiative::class,
            'role_assignments',
            'role_id',
            'roleable_id'
        )->withPivot('user_id', 'roleable_type')
            ->wherePivot('roleable_type', DataInitiative::class)
            ->using(RoleAssignment::class);
    }

    /**
     * Get the business assets where this role is assigned.
     *
     * @return BelongsToMany<BusinessAsset, $this, RoleAssignment>
     */
    public function businessAssets(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'role_assignments',
            'role_id',
            'roleable_id'
        )->withPivot('user_id', 'roleable_type')
            ->wherePivot('roleable_type', BusinessAsset::class)
            ->using(RoleAssignment::class);
    }

    /**
     * Get the data sources where this role is assigned.
     *
     * @return BelongsToMany<DataSource, $this, RoleAssignment>
     */
    public function dataSources(): BelongsToMany
    {
        return $this->belongsToMany(
            DataSource::class,
            'role_assignments',
            'role_id',
            'roleable_id'
        )->withPivot('user_id', 'roleable_type')
            ->wherePivot('roleable_type', DataSource::class)
            ->using(RoleAssignment::class);
    }

    /**
     * Get the domains where this role is assigned.
     *
     * @return BelongsToMany<Domain, $this, RoleAssignment>
     */
    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(
            Domain::class,
            'role_assignments',
            'role_id',
            'roleable_id'
        )->withPivot('user_id', 'roleable_type')
            ->wherePivot('roleable_type', Domain::class)
            ->using(RoleAssignment::class);
    }
}
