<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'mesa_id' => $this->mesa_id,
            'cliente_nombre' => $this->cliente_nombre,
            'cliente_telefono' => $this->cliente_telefono,
            'cliente_email' => $this->cliente_email,
            'comensales' => $this->comensales,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'estado' => $this->estado,
            'canal' => $this->canal,
            'notas' => $this->notas,
            'idempotency_key' => $this->idempotency_key,
            'usuario_id' => $this->usuario_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
