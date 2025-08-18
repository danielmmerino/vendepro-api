<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id','local_id','ambiente','establecimiento','punto_emision','secuencial','numero',
        'fecha_emision','estado','clave_acceso','autorizacion_numero','autorizado_at',
        'cliente_id','cliente_identificacion','cliente_tipo','cliente_razon_social','cliente_email','cliente_direccion',
        'subtotal_0','subtotal_12','subtotal_15','subtotal_exento','subtotal_no_objeto',
        'descuento_total','propina','iva_total','total','observacion','xml_path','pdf_path',
        'created_by','updated_by'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'autorizado_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(FacturaDetalle::class);
    }
}
