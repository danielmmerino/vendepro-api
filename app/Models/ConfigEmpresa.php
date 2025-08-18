<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigEmpresa extends Model
{
    protected $table = 'config_empresa';
    protected $fillable = ['config', 'updated_by'];
    protected $casts = [
        'config' => 'array',
    ];
}

