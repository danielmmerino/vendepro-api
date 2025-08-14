<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuentaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pedido_id' => ['required','uuid','exists:pedidos,id'],
            'nombre' => ['required','string','max:60'],
            'notas' => ['nullable','string','max:255'],
        ];
    }
}
