<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CxcPago extends Model
{
    use HasFactory;
    protected $table = 'cxc_pagos';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'cxc_id','fecha_pago','monto','forma_pago','referencia','usuario_id'
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
