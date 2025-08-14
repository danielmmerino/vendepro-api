<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'cuenta_id',
        'pedido_item_id',
        'cantidad',
        'monto',
        'impuesto_monto',
        'notas',
    ];

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }

    public function pedidoItem(): BelongsTo
    {
        return $this->belongsTo(PedidoItem::class);
    }
}
