<?php

namespace App\Http\Resources;

use App\Models\Regulation;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Regulation
 */
class RegulationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'documentation_url' => $this->documentation_url,
            'business_asset_ids' => $this->whenLoaded('businessAssets', function () {
                return $this->businessAssets->pluck('id');
            }),
            'business_assets' => BusinessAssetResource::collection($this->whenLoaded('businessAssets')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
