<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pedido_id' => ['nullable','uuid'],
            'cliente_id' => ['nullable','uuid'],
            'cuenta_id' => ['nullable','uuid'],
            'numero' => ['required','string','max:40','unique:facturas_venta,numero'],
            'fecha' => ['required','date'],
            'subtotal' => ['required','numeric'],
            'descuento' => ['nullable','numeric'],
            'impuesto' => ['nullable','numeric'],
            'total' => ['required','numeric'],
            'moneda' => ['nullable','string','size:3'],
            'canal' => ['nullable','in:salon,para_llevar,delivery,mostrador'],
            'notas' => ['nullable','string','max:255'],
            'items' => ['required','array','min:1'],
            'items.*.producto_id' => ['required','uuid'],
            'items.*.concepto' => ['required','string','max:160'],
            'items.*.cantidad' => ['required','numeric','gt:0'],
            'items.*.precio_unit' => ['required','numeric','gte:0'],
            'items.*.impuesto_porcentaje' => ['nullable','numeric'],
            'items.*.total_linea' => ['required','numeric'],
        ];
    }
}
