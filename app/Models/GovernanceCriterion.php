<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernanceCriterion extends Model
{
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
