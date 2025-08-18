<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TesEstadoCuentaLinea extends Model
{
    use HasFactory;
    protected $table = 'tes_estados_cuenta_lineas';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['estado_id','fecha','descripcion','referencia','monto','tipo','conciliado','entidad_tipo','entidad_id'];

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
