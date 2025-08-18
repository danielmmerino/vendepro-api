<?php

namespace App\Models\Sri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Establecimiento extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'sri_establecimientos';

    protected $fillable = [
        'emisor_id',
        'codigo',
        'direccion',
        'nombre',
    ];

    public function emisor()
    {
        return $this->belongsTo(Emisor::class);
    }

    public function puntos()
    {
        return $this->hasMany(PuntoEmision::class);
    }
}
