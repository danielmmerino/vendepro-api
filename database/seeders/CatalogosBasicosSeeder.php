<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosBasicosSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = DB::table('empresas')->where('ruc','1790012345001')->value('id');

        // Impuestos (Ecuador común)
        $impuestos = [
            ['empresa_id' => null,      'codigo' => 'IVA12', 'nombre' => 'IVA 12%', 'tipo' => 'IVA', 'porcentaje' => 12.000, 'vigente' => 1],
            ['empresa_id' => null,      'codigo' => 'IVA0',  'nombre' => 'IVA 0%',  'tipo' => 'IVA', 'porcentaje' => 0.000,  'vigente' => 1],
            ['empresa_id' => $empresaId,'codigo' => 'EXE',   'nombre' => 'Exento',  'tipo' => 'OTRO','porcentaje' => 0.000,  'vigente' => 1],
        ];
        foreach ($impuestos as $i) {
            DB::table('impuestos')->updateOrInsert(
                ['codigo' => $i['codigo'], 'empresa_id' => $i['empresa_id']],
                ['nombre' => $i['nombre'], 'tipo' => $i['tipo'], 'porcentaje' => $i['porcentaje'], 'vigente' => $i['vigente']]
            );
        }

        // Unidades de medida
        $unidades = [
            ['empresa_id' => $empresaId, 'nombre' => 'Unidad', 'abreviatura' => 'UND'],
            ['empresa_id' => $empresaId, 'nombre' => 'Kilogramo', 'abreviatura' => 'KG'],
            ['empresa_id' => $empresaId, 'nombre' => 'Litro', 'abreviatura' => 'L'],
            ['empresa_id' => $empresaId, 'nombre' => 'Gramo', 'abreviatura' => 'GR'],
        ];
        foreach ($unidades as $u) {
            DB::table('unidades_medida')->updateOrInsert(
                ['empresa_id' => $u['empresa_id'], 'abreviatura' => $u['abreviatura']],
                ['nombre' => $u['nombre']]
            );
        }

        // Métodos de pago
        $metodos = [
            [ 'empresa_id' => null, 'nombre' => 'EFECTIVO',     'activo' => 1 ],
            [ 'empresa_id' => null, 'nombre' => 'TARJETA',      'activo' => 1 ],
            [ 'empresa_id' => null, 'nombre' => 'TRANSFERENCIA','activo' => 1 ],
            [ 'empresa_id' => null, 'nombre' => 'OTRO',         'activo' => 1 ],
        ];
        foreach ($metodos as $m) {
            DB::table('metodos_pago')->updateOrInsert(
                ['empresa_id' => $m['empresa_id'], 'nombre' => $m['nombre']],
                ['activo' => $m['activo']]
            );
        }

        // Categorías
        $categorias = [
            ['empresa_id' => $empresaId, 'nombre' => 'General', 'padre_id' => null, 'orden' => 1],
            ['empresa_id' => $empresaId, 'nombre' => 'Alimentos', 'padre_id' => null, 'orden' => 2],
            ['empresa_id' => $empresaId, 'nombre' => 'Bebidas', 'padre_id' => null, 'orden' => 3],
            ['empresa_id' => $empresaId, 'nombre' => 'Servicios', 'padre_id' => null, 'orden' => 4],
        ];
        foreach ($categorias as $c) {
            DB::table('categorias_producto')->updateOrInsert(
                ['empresa_id' => $c['empresa_id'], 'nombre' => $c['nombre']],
                ['padre_id' => $c['padre_id'], 'orden' => $c['orden']]
            );
        }
    }
}
