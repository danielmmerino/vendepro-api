<?php

namespace App\Models\Sri;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Log extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;
    protected $table = 'sri_logs';

    protected $fillable = [
        'comprobante_id','fase','request_payload','response_payload','http_code','mensaje','error','created_at'
    ];

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }
}
