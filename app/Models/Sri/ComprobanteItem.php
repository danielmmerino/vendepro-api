<?php

namespace App\Models\Sri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ComprobanteItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sri_comprobante_items';

    protected $fillable = [
        'comprobante_id','codigo_principal','descripcion','cantidad','precio_unit','descuento',
        'impuesto_codigo','impuesto_tarifa','impuesto_base','impuesto_valor','orden'
    ];

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }
}
