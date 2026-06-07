<?php

namespace App\Http\Resources\Denah;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_slots' => $this->resource['total_slots'] ?? 0,
            'filled_slots' => $this->resource['filled_slots'] ?? 0,
            'empty_slots' => $this->resource['empty_slots'] ?? 0,
            'fill_rate' => round($this->resource['fill_rate'] ?? 0, 2),
        ];
    }
}
