<?php

namespace App\Http\Resources\Sri;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Sri\Secuencia */
class SecuenciaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'secuencial' => $this->resource,
        ];
    }
}
