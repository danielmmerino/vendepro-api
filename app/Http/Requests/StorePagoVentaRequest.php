<?php

namespace App\Http\Requests;

class StorePagoVentaRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'factura_id' => ['required','integer'],
            'cliente_id' => ['nullable','integer'],
            'items_pago' => ['required','array','min:1'],
            'items_pago.*.metodo' => ['required','string'],
            'items_pago.*.monto' => ['required','numeric'],
            'items_pago.*.tarjeta' => ['nullable','array'],
            'items_pago.*.transferencia' => ['nullable','array'],
            'propina' => ['nullable','numeric'],
            'redondeo' => ['nullable','numeric'],
            'caja.apertura_id' => ['nullable','integer'],
            'observacion' => ['nullable','string'],
            'idempotency_key' => ['nullable','string'],
        ];
    }
}
