<?php

namespace App\Http\Requests\Sri;

use Illuminate\Foundation\Http\FormRequest;

class NextSecuenciaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'emisor_id' => ['required','uuid'],
            'establecimiento' => ['required','string','size:3'],
            'punto' => ['required','string','size:3'],
            'tipo' => ['required','in:01,04,05,06,07'],
        ];
    }
}
