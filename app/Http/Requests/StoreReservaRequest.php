<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mesa_id' => ['required','uuid'],
            'cliente_nombre' => ['required','string','max:120'],
            'cliente_telefono' => ['nullable','string','max:40'],
            'cliente_email' => ['nullable','email','max:120'],
            'comensales' => ['required','integer','min:1'],
            'inicio' => ['required','date'],
            'fin' => ['required','date'],
            'estado' => ['nullable','in:pendiente,confirmada,cancelada,no_show'],
            'canal' => ['nullable','in:telefono,web,app,walk_in,tercero'],
            'notas' => ['nullable','string','max:255'],
            'usuario_id' => ['nullable','uuid'],
        ];
    }
}
