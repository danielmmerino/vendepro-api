<?php

namespace App\Http\Requests;

class UpdateProveedorRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'empresa_id' => ['required', 'integer'],
            'identificacion' => ['required', 'string', 'max:20'],
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
