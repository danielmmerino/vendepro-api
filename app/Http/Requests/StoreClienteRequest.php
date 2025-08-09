<?php

namespace App\Http\Requests;

class StoreClienteRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'empresa_id' => ['required', 'integer'],
            'tipo_id' => ['nullable', 'in:CEDULA,RUC,PASAPORTE,CONSUMIDOR_FINAL'],
            'identificacion' => ['nullable', 'string', 'max:20', 'required_unless:tipo_id,CONSUMIDOR_FINAL'],
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'es_activo' => ['boolean'],
        ];
    }
}
