<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaMovimiento extends Model
{
    protected $table = 'caja_movimientos';

    protected $fillable = [
        'apertura_id','tipo','monto','motivo','referencia','banco','fecha_deposito'
    ];

    protected $casts = [
        'fecha_deposito' => 'date',
    ];
}
