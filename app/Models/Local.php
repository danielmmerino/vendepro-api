<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Local extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'locales';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'empresa_id',
        'nombre',
        'direccion',
        'telefono',
        'codigo_establecimiento',
        'codigo_punto_emision',
        'secuencial_factura',
        'secuencial_nc',
        'secuencial_retencion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }
}
