<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComandaDetalle extends Model
{
    use HasUuids;

    protected $table = 'comandas_detalle';

    protected $fillable = [
        'comanda_id',
        'item_id',
        'cantidad',
    ];

    public function comanda(): BelongsTo
    {
        return $this->belongsTo(Comanda::class);
    }
}
