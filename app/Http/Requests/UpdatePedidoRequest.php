<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mesa_id' => ['nullable','uuid'],
            'cliente_nombre' => ['nullable','string','max:120'],
            'origen' => ['required','in:salon,para_llevar,delivery'],
            'notas' => ['nullable','string','max:255'],
        ];
    }
}
