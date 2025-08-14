<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuenta extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'pedido_id',
        'nombre',
        'notas',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'estado',
        'created_by',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CuentaItem::class);
    }
}
