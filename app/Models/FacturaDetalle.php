<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    use HasFactory;

    protected $table = 'factura_detalles';

    protected $fillable = [
        'factura_id','producto_id','descripcion','cantidad','precio_unitario','descuento',
        'impuesto_codigo','impuesto_tarifa','iva_monto','total_linea'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}
