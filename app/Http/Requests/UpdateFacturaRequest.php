<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'cliente_id' => ['nullable','uuid'],
            'numero' => ['required','string','max:40', Rule::unique('facturas_venta','numero')->ignore($id)],
            'fecha' => ['required','date'],
            'subtotal' => ['required','numeric'],
            'descuento' => ['nullable','numeric'],
            'impuesto' => ['nullable','numeric'],
            'total' => ['required','numeric'],
            'notas' => ['nullable','string','max:255'],
            'items' => ['required','array','min:1'],
            'items.*.id' => ['nullable','uuid'],
            'items.*.producto_id' => ['required','uuid'],
            'items.*.concepto' => ['required','string','max:160'],
            'items.*.cantidad' => ['required','numeric','gt:0'],
            'items.*.precio_unit' => ['required','numeric','gte:0'],
            'items.*.impuesto_porcentaje' => ['nullable','numeric'],
            'items.*.total_linea' => ['required','numeric'],
        ];
    }
}
