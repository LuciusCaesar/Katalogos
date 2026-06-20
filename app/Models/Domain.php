<?php

namespace App\Models;

use Database\Factories\DomainFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
