<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Mesa */
class MesaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'local_id' => $this->local_id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'capacidad' => $this->capacidad,
            'ubicacion' => $this->ubicacion,
            'estado' => $this->estado,
            'notas' => $this->notas,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
