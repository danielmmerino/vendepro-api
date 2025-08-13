<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MesaCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => MesaResource::collection($this->collection),
            'pagination' => [
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
        ];
    }
}
