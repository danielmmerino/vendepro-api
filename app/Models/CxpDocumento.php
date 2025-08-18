<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CxpDocumento extends Model
{
    use HasFactory;
    protected $table = 'cuentas_por_pagar';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'compra_id','proveedor_id','fecha_emision','fecha_vencimiento','total','saldo_pendiente','estado'
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

    public function pagos()
    {
        return $this->hasMany(CxpPago::class, 'cxp_id');
    }
}
