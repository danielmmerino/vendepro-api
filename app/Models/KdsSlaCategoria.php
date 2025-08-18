<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KdsSlaCategoria extends Model
{
    protected $table = 'kds_sla_categoria';

    protected $fillable = [
        'categoria_id',
        'sla_seg',
    ];
}
