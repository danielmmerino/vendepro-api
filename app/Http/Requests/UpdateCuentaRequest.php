<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCuentaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:60'],
            'notas' => ['nullable','string','max:255'],
            'descuento' => ['nullable','numeric','min:0'],
            'estado' => ['nullable','in:abierta,cerrada,anulada'],
        ];
    }
}
