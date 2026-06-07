<?php

namespace App\Http\Resources\Denah;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantDenahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'nama' => $this->nama_tenant,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi ?? '',
            'foto' => $this->foto,
        ];
    }
}
