<?php

namespace App\Http\Resources\Denah;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DenahCollectionResource extends ResourceCollection
{
    public $collects = DenahResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'total' => $this->collection->count(),
        ];
    }
}
