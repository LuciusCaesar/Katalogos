<?php

namespace App\Models;

use Database\Factories\GovernanceCriterionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property float $weight
 * @property string|null $category
 * @property bool $is_active
 * @property int $order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @mixin GovernanceCriterionFactory
 */
class GovernanceCriterion extends Model
{
    /** @use HasFactory<GovernanceCriterionFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'weight',
        'category',
        'is_active',
        'order',
    ];

    protected $casts = [
        'weight' => 'decimal:4',
        'is_active' => 'boolean',
    ];
}
