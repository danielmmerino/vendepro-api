<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mesa_id' => $this->mesa_id,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'estado' => $this->estado,
        ];
    }
}
