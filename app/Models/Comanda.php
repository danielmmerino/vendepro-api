<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comanda extends Model
{
    use HasUuids;

    protected $fillable = [
        'pedido_id',
        'estacion_id',
        'estado',
        'curso',
        'start_at',
        'ready_at',
        'served_at',
        'recall_at',
        'sla_seg_objetivo',
        'prep_time_seg',
        'reassigned_from',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(ComandaDetalle::class);
    }
}
