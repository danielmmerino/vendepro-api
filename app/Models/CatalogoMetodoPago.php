<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoMetodoPago extends Model
{
    protected $table = 'catalogo_metodos_pago';

    protected $fillable = ['codigo','nombre','activo'];
}
