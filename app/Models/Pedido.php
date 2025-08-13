<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'mesa_id',
        'cliente_nombre',
        'estado',
        'origen',
        'notas',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'created_by',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }
}
