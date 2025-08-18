<?php

namespace App\Models\Sri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Comprobante extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'sri_comprobantes';

    protected $fillable = [
        'tipo','emisor_id','establecimiento','punto_emision','secuencial','numero','fecha_emision',
        'clave_acceso','ambiente','tipo_emision','estado','fuente','fuente_id','receptor_identificacion',
        'receptor_razon_social','total_sin_impuestos','total_descuento','propina','importe_total','moneda',
        'xml_generado','xml_firmado','nro_autorizacion','fecha_autorizacion','ride_pdf_path','ultimo_error',
        'certificado_id','reintentos_envio','reintentos_autorizacion','created_by'
    ];

    public function items()
    {
        return $this->hasMany(ComprobanteItem::class);
    }
}
