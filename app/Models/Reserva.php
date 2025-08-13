<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'mesa_id',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_email',
        'comensales',
        'inicio',
        'fin',
        'estado',
        'canal',
        'notas',
        'idempotency_key',
        'usuario_id',
    ];
}
