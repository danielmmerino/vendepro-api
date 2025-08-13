<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mesa_id' => ['sometimes','uuid'],
            'cliente_nombre' => ['sometimes','string','max:120'],
            'cliente_telefono' => ['sometimes','nullable','string','max:40'],
            'cliente_email' => ['sometimes','nullable','email','max:120'],
            'comensales' => ['sometimes','integer','min:1'],
            'inicio' => ['sometimes','date'],
            'fin' => ['sometimes','date'],
            'estado' => ['sometimes','in:pendiente,confirmada,cancelada,no_show'],
            'canal' => ['sometimes','in:telefono,web,app,walk_in,tercero'],
            'notas' => ['sometimes','nullable','string','max:255'],
            'usuario_id' => ['sometimes','nullable','uuid'],
        ];
    }
}
