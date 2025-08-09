<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalesSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = DB::table('empresas')->where('ruc','1790012345001')->value('id');

        DB::table('locales')->updateOrInsert(
            [
                'empresa_id' => $empresaId,
                'codigo_establecimiento' => '001',
                'codigo_punto_emision'   => '001',
            ],
            [
                'nombre' => 'Matriz Quito',
                'direccion' => 'Av. Siempre Viva 123',
                'telefono' => '022-000000',
                'secuencial_factura' => 1,
                'secuencial_nc' => 1,
                'secuencial_retencion' => 1,
                'activo' => 1,
            ]
        );
    }
}
