<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subscription_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'subscription_expires_at' => 'datetime',
        ];
    }
}
