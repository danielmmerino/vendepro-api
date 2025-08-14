<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cxc_id' => ['required','uuid'],
            'fecha_pago' => ['required','date'],
            'monto' => ['required','numeric','gt:0'],
            'forma_pago' => ['nullable','in:efectivo,tarjeta,transferencia,otros'],
            'referencia' => ['nullable','string','max:100'],
        ];
    }
}
