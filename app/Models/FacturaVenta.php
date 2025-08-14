<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FacturaVenta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facturas_venta';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'pedido_id', 'cliente_id', 'cuenta_id', 'numero', 'fecha',
        'subtotal','descuento','impuesto','total','moneda','estado',
        'canal','notas','usuario_id'
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

    public function items()
    {
        return $this->hasMany(FacturaItem::class, 'factura_id');
    }

    public function cxc()
    {
        return $this->hasOne(CxcDocumento::class, 'factura_id');
    }
}
