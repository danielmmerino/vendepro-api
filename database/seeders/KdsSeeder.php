<?php

namespace Database\Seeders;

use App\Models\KdsEstacion;
use Illuminate\Database\Seeder;

class KdsSeeder extends Seeder
{
    public function run(): void
    {
        KdsEstacion::firstOrCreate(
            ['nombre' => 'Cocina Demo'],
            ['local_id' => 1, 'orden' => 1]
        );
    }
}
