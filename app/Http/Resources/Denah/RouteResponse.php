<?php

namespace App\Http\Resources\Denah;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResponse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'path' => $this->resource['path'] ?? [],
            'distance' => $this->resource['distance'] ?? 0,
            'node_count' => count($this->resource['path'] ?? []),
            'start_node_id' => $this->resource['start_node_id'] ?? null,
            'end_node_id' => $this->resource['end_node_id'] ?? null,
        ];
    }
}
