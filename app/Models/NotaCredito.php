<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NotaCredito extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cxc_notas_credito';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'factura_id','numero','fecha','motivo','subtotal','impuesto','total','estado'
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
