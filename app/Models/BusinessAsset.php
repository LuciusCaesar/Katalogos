<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'definition',
        'data_initiative_id',
    ];

    public function dataInitiative()
    {
        return $this->belongsTo(DataInitiative::class);
    }
}
