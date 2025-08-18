<?php

namespace App\Http\Controllers\Sri;

use App\Http\Controllers\Controller;

class EstadoController extends Controller
{
    public function show($clave)
    {
        return ['data'=>[
            'estado'=>'AUTORIZADO',
            'mensajes'=>[]
        ]];
    }
}
