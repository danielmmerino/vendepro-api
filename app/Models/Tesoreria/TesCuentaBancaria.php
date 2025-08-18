<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TesCuentaBancaria extends Model
{
    use HasFactory;
    protected $table = 'tes_cuentas_bancarias';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['banco','numero','moneda','alias','activo'];

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
