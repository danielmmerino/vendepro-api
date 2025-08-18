<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PedidoItemMod extends Model
{
    use HasUuids;

    protected $table = 'pedido_item_mods';

    protected $fillable = [
        'item_id',
        'modificador_id',
        'nombre',
        'precio',
    ];
}
