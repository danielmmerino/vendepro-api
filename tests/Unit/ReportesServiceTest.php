<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ReportesService;

class ReportesServiceTest extends TestCase
{
    public function test_calcula_kpis_correctamente(): void
    {
        $service = new ReportesService();
        $kpis = $service->calcularKpis(100, 10, 12, 5, 5, 50);

        $this->assertEquals(90.0, $kpis['ventas_netas']);
        $this->assertEquals(107.0, $kpis['ventas_totales']);
        $this->assertEquals(21.4, $kpis['ticket_promedio']);
        $this->assertEquals(40.0, $kpis['margen']);
    }
}
