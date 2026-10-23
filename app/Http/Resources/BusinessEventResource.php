<?php

namespace App\Http\Resources;

use App\Models\BusinessEvent;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BusinessEvent
 */
class BusinessEventResource extends JsonResource
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
            'business_asset_ids' => $this->whenLoaded('businessAssets', function () {
                return $this->businessAssets->pluck('id');
            }),
            'business_assets' => BusinessAssetResource::collection($this->whenLoaded('businessAssets')),
            'business_process_ids' => $this->whenLoaded('businessProcesses', function () {
                return $this->businessProcesses->pluck('id');
            }),
            'business_processes' => BusinessProcessResource::collection($this->whenLoaded('businessProcesses')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
