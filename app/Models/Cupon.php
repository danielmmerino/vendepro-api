<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $fillable = [
        'promo_id','codigo','vigencia_hasta','uso_max_global','uso_max_por_cliente','estado','usos_realizados'
    ];

    protected $casts = [
        'vigencia_hasta' => 'datetime',
    ];

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'promo_id');
    }
}
