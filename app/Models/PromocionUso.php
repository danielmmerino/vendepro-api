<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromocionUso extends Model
{
    protected $table = 'promociones_usos';

    protected $fillable = [
        'promocion_id','cliente_id','pedido_id','factura_id','monto_descuento','usado_at'
    ];

    protected $casts = [
        'usado_at' => 'datetime',
    ];
}
