<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CxpPago extends Model
{
    use HasFactory;
    protected $table = 'pagos_proveedor';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'cxp_id','fecha_pago','monto','forma_pago','referencia'
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
