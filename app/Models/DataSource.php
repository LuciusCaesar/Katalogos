<?php

namespace App\Models;

use Database\Factories\DataSourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DataSource extends Model
{
    /** @use HasFactory<DataSourceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all business assets associated with this data source.
     *
     * @return BelongsToMany<BusinessAsset, $this>
     */
    public function businessAssets(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'business_asset_data_source',
            'data_source_id',
            'business_asset_id'
        );
    }

    /**
     * Get all data quality checks associated with this data source.
     *
     * @return BelongsToMany<DataQualityCheck, $this>
     */
    public function dataQualityChecks(): BelongsToMany
    {
        return $this->belongsToMany(
            DataQualityCheck::class,
            'data_quality_check_data_source',
            'data_source_id',
            'data_quality_check_id'
        );
    }

    /**
     * Get all role assignments for this data source.
     *
     * @return MorphMany<RoleAssignment, $this>
     */
    public function roleAssignments(): MorphMany
    {
        return $this->morphMany(RoleAssignment::class, 'roleable');
    }

    /**
     * Get all users assigned to this data source with any role.
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
     * Get all roles assigned to this data source.
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
     * Get the Data Custodian for this data source.
     *
     * @return BelongsToMany<User, $this, RoleAssignment>
     */
    public function dataCustodian(): BelongsToMany
    {
        return $this->users()->wherePivot('role_id', function ($query) {
            $query->select('id')->from('roles')->where('name', 'Data Custodian');
        });
    }

    /**
     * Assign a role to a user for this data source.
     */
    public function assignRoleToUser(User $user, Role $role): RoleAssignment
    {
        return $this->roleAssignments()->create([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
    }

    /**
     * Remove a role assignment from a user for this data source.
     */
    public function removeRoleFromUser(User $user, Role $role): bool
    {
        return $this->roleAssignments()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->delete() > 0;
    }
}
