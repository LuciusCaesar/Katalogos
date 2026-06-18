<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataInitiative extends Model
{
    /** @use HasFactory<\Database\Factories\DataInitiativeFactory> */
    use HasFactory;

    protected $fillable = ['code', 'label', 'description'];

    public function businessAssets()
    {
        return $this->hasMany(BusinessAsset::class);
    }
}
