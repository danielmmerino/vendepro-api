<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoVenta extends Model
{
    protected $table = 'pagos_venta';

    protected $fillable = [
        'factura_id','total','propina','redondeo','apertura_id','estado','idempotency_key'
    ];

    public function detalles()
    {
        return $this->hasMany(PagoVentaDetalle::class, 'pago_id');
    }
}
