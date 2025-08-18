<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CajaApertura extends Model
{
    use SoftDeletes;

    protected $table = 'caja_aperturas';

    protected $fillable = [
        'local_id','caja_id','usuario_id','saldo_inicial','abierto_at','cerrado_at','estado','observacion'
    ];

    protected $casts = [
        'abierto_at' => 'datetime',
        'cerrado_at' => 'datetime',
    ];
}
