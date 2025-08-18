<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KdsEvento extends Model
{
    use HasUuids;

    protected $table = 'kds_eventos';

    protected $fillable = [
        'comanda_id',
        'tipo',
        'usuario_id',
        'detalle',
    ];

    protected $casts = [
        'detalle' => 'array',
    ];

    public function comanda(): BelongsTo
    {
        return $this->belongsTo(Comanda::class);
    }
}
