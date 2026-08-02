<?php

namespace App\Models;

use Database\Factories\BusinessProcessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class BusinessProcess extends Model
{
    /** @use HasFactory<BusinessProcessFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all business assets associated with this business process.
     *
     * @return BelongsToMany<BusinessAsset, $this>
     */
    public function businessAssets(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'business_asset_business_process',
            'business_process_id',
            'business_asset_id'
        );
    }
}
