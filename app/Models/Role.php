<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Get the users that have this role assigned.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_assignments')
            ->withPivot('roleable_id', 'roleable_type')
            ->using(RoleAssignment::class);
    }

    /**
     * Get the data initiatives where this role is assigned.
     */
    public function dataInitiatives()
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
     */
    public function businessAssets()
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
}
