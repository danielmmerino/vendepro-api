<?php

namespace App\Http\Requests;

class StoreCompraRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'uuid', 'exists:proveedores,id'],
            'fecha' => ['required', 'date'],
            'numero_factura' => ['required', 'string', 'max:50'],
            'subtotal' => ['required', 'numeric'],
            'descuento' => ['nullable', 'numeric'],
            'impuesto' => ['nullable', 'numeric'],
            'total' => ['required', 'numeric'],
            'estado' => ['nullable', 'in:borrador,aprobada,anulada'],
            'observacion' => ['nullable', 'string', 'max:255'],
            'usuario_id' => ['nullable', 'uuid', 'exists:users,id'],
        ];
    }
}
