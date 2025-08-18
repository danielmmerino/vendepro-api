<?php

namespace App\Models\Sri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Emisor extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'sri_emisores';

    protected $fillable = [
        'ruc',
        'razon_social',
        'nombre_comercial',
        'contribuyente_especial',
        'obligado_contabilidad',
        'direccion_matriz',
        'ambiente',
        'tipo_emision',
        'email_contacto',
        'telefono',
    ];

    public function establecimientos()
    {
        return $this->hasMany(Establecimiento::class);
    }
}
