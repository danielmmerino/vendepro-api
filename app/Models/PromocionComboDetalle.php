<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromocionComboDetalle extends Model
{
    protected $table = 'promociones_combo_detalle';

    protected $fillable = [
        'promocion_id','producto_id','cantidad'
    ];
}
