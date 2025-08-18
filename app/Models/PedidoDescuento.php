<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDescuento extends Model
{
    protected $table = 'pedidos_descuentos';

    protected $fillable = [
        'pedido_id','promocion_id','tipo','base','valor','nivel','linea_producto_id'
    ];
}
