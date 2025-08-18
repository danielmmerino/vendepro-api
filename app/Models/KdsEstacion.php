<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class KdsEstacion extends Model
{
    use HasUuids;

    protected $table = 'kds_estaciones';

    protected $fillable = [
        'local_id',
        'nombre',
        'categorias',
        'productos_override',
        'impresora_id',
        'orden',
        'sonido_alertas',
        'color_acento',
        'activo',
    ];

    protected $casts = [
        'categorias' => 'array',
        'productos_override' => 'array',
        'sonido_alertas' => 'boolean',
        'activo' => 'boolean',
    ];
}
