<?php

namespace App\Http\Resources\Denah;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DenahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'denah_id' => $this->denah_id,
            'blok' => $this->blok,
            'status' => $this->status,
            'posisi_x' => $this->posisi_x,
            'posisi_y' => $this->posisi_y,
            'tenant_id' => $this->tenant_id,
            'tenant' => new TenantDenahResource($this->whenLoaded('tenant')),
        ];
    }
}
