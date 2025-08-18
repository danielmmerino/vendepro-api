<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'precio_mensual',
        'precio_anual',
        'trial_dias',
        'limites',
        'features',
        'activo',
    ];

    protected $casts = [
        'limites' => 'array',
        'features' => 'array',
        'precio_mensual' => 'decimal:2',
        'precio_anual' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
