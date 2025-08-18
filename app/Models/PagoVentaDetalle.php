<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoVentaDetalle extends Model
{
    protected $table = 'pagos_venta_detalle';

    protected $fillable = ['pago_id','metodo','monto','detalle'];

    protected $casts = [
        'detalle' => 'array',
    ];
}
