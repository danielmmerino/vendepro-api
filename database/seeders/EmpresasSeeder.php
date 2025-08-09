<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('empresas')->updateOrInsert(
            ['ruc' => '1790012345001'],
            [
                'razon_social'     => 'VendePro S.A.',
                'nombre_comercial' => 'VendePro',
                'email'            => 'admin@vendepro.io',
                'telefono'         => '+593 99 999 9999',
                'direccion'        => 'Quito, Ecuador',
                'pais'             => 'EC',
                'provincia'        => 'Pichincha',
                'ciudad'           => 'Quito',
                'activo'           => 1,
            ]
        );
    }
}
