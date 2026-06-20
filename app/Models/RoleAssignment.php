<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property int $roleable_id
 * @property string $roleable_type
 */
class RoleAssignment extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_assignments';
    /**
     * Get the user that owns the role assignment.
     *
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role that is assigned.
     *
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<Role, $this>
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the roleable entity (DataInitiative, BusinessAsset, etc.).
     *
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function roleable()
    {
        return $this->morphTo();
    }
}
