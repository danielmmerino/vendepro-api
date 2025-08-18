<?php

namespace App\Models\Sri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PuntoEmision extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'sri_puntos_emision';

    protected $fillable = [
        'establecimiento_id',
        'codigo',
        'sec_hasta',
    ];

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function secuencias()
    {
        return $this->hasMany(Secuencia::class);
    }
}
