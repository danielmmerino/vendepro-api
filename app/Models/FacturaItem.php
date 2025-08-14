<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FacturaItem extends Model
{
    use HasFactory;
    protected $table = 'factura_items';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'factura_id','pedido_item_id','producto_id','concepto','cantidad',
        'precio_unit','impuesto_porcentaje','total_linea','notas'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
