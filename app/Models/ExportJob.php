<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    protected $table = 'exports_jobs';

    protected $fillable = [
        'reporte',
        'formato',
        'filtros_json',
        'estado',
        'file_path',
    ];
}
