<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    /** @use HasFactory<\Database\Factories\DomainFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<BusinessAsset, $this>
     */
    public function businessAssets()
    {
        return $this->hasMany(BusinessAsset::class);
    }
}
