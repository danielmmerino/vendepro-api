<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TesEstadoCuenta extends Model
{
    use HasFactory;
    protected $table = 'tes_estados_cuenta';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['cuenta_bancaria_id','periodo','saldo_inicial','saldo_final'];

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
