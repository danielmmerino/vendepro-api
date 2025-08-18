<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SriSeeder extends Seeder
{
    public function run(): void
    {
        $emisorId = Str::uuid();
        DB::table('sri_emisores')->insert([
            'id' => $emisorId,
            'ruc' => '9999999999999',
            'razon_social' => 'Demo SRI',
            'direccion_matriz' => 'Quito',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estabId = Str::uuid();
        DB::table('sri_establecimientos')->insert([
            'id' => $estabId,
            'emisor_id' => $emisorId,
            'codigo' => '001',
            'direccion' => 'Quito',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $puntoId = Str::uuid();
        DB::table('sri_puntos_emision')->insert([
            'id' => $puntoId,
            'establecimiento_id' => $estabId,
            'codigo' => '001',
            'sec_hasta' => 999999999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['01','04','05','06','07'] as $tipo) {
            DB::table('sri_secuencias')->insert([
                'id' => Str::uuid(),
                'punto_emision_id' => $puntoId,
                'tipo' => $tipo,
                'actual' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('sri_certificados')->insert([
            'id' => Str::uuid(),
            'emisor_id' => $emisorId,
            'alias' => 'demo',
            'p12_base64' => '',
            'p12_password' => encrypt(''),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
