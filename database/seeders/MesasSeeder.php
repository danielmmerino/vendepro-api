<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MesasSeeder extends Seeder
{
    public function run(): void
    {
        $localId = DB::table('locales')->value('id');
        if (!$localId) return;

        for ($i=1; $i<=12; $i++) {
            DB::table('mesas')->updateOrInsert(
                ['local_id' => $localId, 'numero' => str_pad($i,2,'0',STR_PAD_LEFT)],
                ['capacidad' => 4, 'estado' => 'DISPONIBLE', 'ubicacion' => 'Salón']
            );
        }
    }
}
