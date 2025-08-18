<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigLocal extends Model
{
    protected $table = 'config_local';
    protected $primaryKey = 'local_id';
    public $incrementing = false;
    protected $fillable = ['local_id','config','updated_by'];
    protected $casts = [
        'config' => 'array',
    ];
}

