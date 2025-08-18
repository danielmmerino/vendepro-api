<?php

namespace App\Models\Sri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Secuencia extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sri_secuencias';

    protected $fillable = [
        'punto_emision_id',
        'tipo',
        'actual',
        'bloqueado',
    ];

    public function punto()
    {
        return $this->belongsTo(PuntoEmision::class, 'punto_emision_id');
    }
}
