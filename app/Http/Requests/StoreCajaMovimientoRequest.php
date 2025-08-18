<?php

namespace App\Http\Requests;

class StoreCajaMovimientoRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'apertura_id' => ['required','integer'],
            'tipo' => ['required','in:ingreso,egreso,ajuste,deposito,venta,propina,cambio'],
            'monto' => ['required','numeric'],
            'motivo' => ['required','string'],
            'referencia' => ['nullable','string'],
            'banco' => ['nullable','string'],
            'fecha_deposito' => ['nullable','date'],
        ];
    }
}
