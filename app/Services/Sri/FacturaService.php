<?php

namespace App\Services\Sri;

use App\Models\Factura;
use Illuminate\Support\Str;

class FacturaService
{
    public function emitir(Factura $factura): array
    {
        // Simula generación de número y clave de acceso
        $factura->secuencial = $factura->secuencial ?? $factura->id;
        $factura->numero = sprintf('%03s-%03s-%09d', $factura->establecimiento, $factura->punto_emision, $factura->secuencial);
        $factura->clave_acceso = Str::upper(Str::random(20));
        $factura->estado = 'autorizada';
        $factura->autorizado_at = now();
        $factura->save();

        return [
            'factura_id' => $factura->id,
            'numero' => $factura->numero,
            'clave_acceso' => $factura->clave_acceso,
            'estado_sri' => 'AUTORIZADO',
            'mensajes' => ['AUTORIZADO'],
            'autorizacion_numero' => '0000000000',
            'autorizado_at' => $factura->autorizado_at->toIso8601String(),
        ];
    }

    public function reintentarEnvio(Factura $factura): array
    {
        return ['estado' => $factura->estado];
    }

    public function consultarEstado(Factura $factura): array
    {
        return [
            'estado' => $factura->estado === 'autorizada' ? 'AUTORIZADO' : strtoupper($factura->estado)
        ];
    }

    public function obtenerXml(Factura $factura): string
    {
        return '<xml>Factura '.$factura->numero.'</xml>';
    }

    public function generarPdf(Factura $factura, string $formato): string
    {
        return 'PDF '.($formato ?: 'A4');
    }
}
