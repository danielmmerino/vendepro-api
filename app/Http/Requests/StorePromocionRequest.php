<?php

namespace App\Http\Requests;

class StorePromocionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => 'required|string',
            'tipo' => 'required|string',
            'prioridad' => 'required|integer',
            'estado' => 'required|string',
            'canal' => 'required|string',
            'vigencia_desde' => 'nullable|date',
            'vigencia_hasta' => 'nullable|date',
            'dias_semana' => 'nullable|array',
            'hora_desde' => 'nullable',
            'hora_hasta' => 'nullable',
            'condiciones' => 'nullable|array',
            'recompensa' => 'nullable|array',
            'limites' => 'nullable|array',
            'stackable' => 'boolean',
            'exclusividad_grupo' => 'nullable|string',
            'requiere_cupon' => 'boolean',
        ];
    }
}
