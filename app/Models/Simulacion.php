<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulacion extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','payload','resultado','vence_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'resultado' => 'array',
        'vence_at' => 'datetime',
    ];
}
