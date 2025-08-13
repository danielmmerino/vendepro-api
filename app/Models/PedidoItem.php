<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItem extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'nombre',
        'cantidad',
        'precio_unit',
        'impuesto_porcentaje',
        'notas',
        'estado',
        'estacion',
        'orden_sec',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
