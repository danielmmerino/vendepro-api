<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaCierre extends Model
{
    protected $table = 'caja_cierres';

    protected $fillable = [
        'apertura_id','esperado_efectivo','contado_efectivo','diferencia','detalle_conteo','observacion'
    ];

    protected $casts = [
        'detalle_conteo' => 'array',
    ];
}
