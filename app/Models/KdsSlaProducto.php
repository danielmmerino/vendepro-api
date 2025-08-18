<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KdsSlaProducto extends Model
{
    protected $table = 'kds_sla_producto';

    protected $fillable = [
        'producto_id',
        'sla_seg',
    ];
}
