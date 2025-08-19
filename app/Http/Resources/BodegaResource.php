<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Bodega */
class BodegaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'local_id' => $this->local_id,
            'nombre' => $this->nombre,
            'es_principal' => $this->es_principal,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
