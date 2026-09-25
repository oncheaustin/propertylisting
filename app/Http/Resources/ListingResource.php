<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => (float) $this->price,
            'type' => $this->type,
            'bedrooms' => $this->bedrooms,
            'location' => [
                'address' => $this->location,
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ],
            'agent_id' => $this->agent_id,
            'distance_km' => $this->when(isset($this->distance_km), fn () => round((float) $this->distance_km, 2)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
