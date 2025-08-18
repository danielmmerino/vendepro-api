<?php

namespace App\Http\Requests;

class PromocionReglaRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'tipo' => 'required|string',
            'condiciones' => 'required|array',
            'recompensa' => 'required|array',
        ];
    }
}
