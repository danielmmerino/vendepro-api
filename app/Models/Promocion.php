<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promocion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre','tipo','prioridad','estado','canal',
        'vigencia_desde','vigencia_hasta','dias_semana','hora_desde','hora_hasta','zona_horaria',
        'condiciones','recompensa','limites','stackable','exclusividad_grupo','requiere_cupon',
        'uso_global','uso_por_cliente_habilitado'
    ];

    protected $casts = [
        'vigencia_desde' => 'datetime',
        'vigencia_hasta' => 'datetime',
        'dias_semana' => 'array',
        'hora_desde' => 'datetime:H:i',
        'hora_hasta' => 'datetime:H:i',
        'condiciones' => 'array',
        'recompensa' => 'array',
        'limites' => 'array',
        'stackable' => 'boolean',
        'requiere_cupon' => 'boolean',
        'uso_por_cliente_habilitado' => 'boolean',
    ];
}
