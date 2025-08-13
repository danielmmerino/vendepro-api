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
            'mesa_id' => ['required', 'uuid'],
            'inicio' => ['required', 'date'],
            'fin' => ['required', 'date', 'after:inicio'],
        ];
    }
}
