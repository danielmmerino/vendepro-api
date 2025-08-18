<?php

namespace App\Models\Sri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Certificado extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'sri_certificados';

    protected $fillable = [
        'emisor_id',
        'alias',
        'p12_base64',
        'p12_password',
        'valido_desde',
        'valido_hasta',
        'activo',
    ];

    public function emisor()
    {
        return $this->belongsTo(Emisor::class);
    }
}
