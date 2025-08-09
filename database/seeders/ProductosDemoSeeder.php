<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = DB::table('empresas')->where('ruc','1790012345001')->value('id');
        $catGen    = DB::table('categorias_producto')->where('empresa_id',$empresaId)->where('nombre','General')->value('id');
        $und       = DB::table('unidades_medida')->where('empresa_id',$empresaId)->where('abreviatura','UND')->value('id');
        $iva12     = DB::table('impuestos')->whereNull('empresa_id')->where('codigo','IVA12')->value('id');

        $items = [
            ['codigo'=>'PROD-001','nombre'=>'Producto genérico 1','precio_venta'=>5.50],
            ['codigo'=>'PROD-002','nombre'=>'Producto genérico 2','precio_venta'=>9.90],
        ];

        foreach ($items as $it) {
            DB::table('productos')->updateOrInsert(
                ['empresa_id'=>$empresaId, 'codigo'=>$it['codigo']],
                [
                    'categoria_id'=>$catGen,
                    'nombre'=>$it['nombre'],
                    'descripcion'=>null,
                    'tipo'=>'BIEN',
                    'sku'=>$it['codigo'],
                    'unidad_id'=>$und,
                    'impuesto_id'=>$iva12,
                    'precio_venta'=>$it['precio_venta'],
                    'costo_promedio'=>0,
                    'es_receta'=>0,
                    'activo'=>1
                ]
            );
        }
    }
}
