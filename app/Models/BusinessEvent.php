<?php

namespace App\Models;

use Database\Factories\BusinessEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Laravel\Scout\Searchable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class BusinessEvent extends Model
{
    /** @use HasFactory<BusinessEventFactory> */
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, string|null>
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    /**
     * Get all business assets associated with this business event.
     *
     * The pivot carries a nullable relation_type column reserved for the
     * future "subject of" / "context of" distinction; this undifferentiated
     * relation covers every pivot row.
     *
     * @return BelongsToMany<BusinessAsset, $this>
     */
    public function businessAssets(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessAsset::class,
            'business_asset_business_event',
            'business_event_id',
            'business_asset_id'
        );
    }

    /**
     * Get all business processes associated with this business event.
     *
     * @return BelongsToMany<BusinessProcess, $this>
     */
    public function businessProcesses(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessProcess::class,
            'business_event_business_process',
            'business_event_id',
            'business_process_id'
        );
    }
}
