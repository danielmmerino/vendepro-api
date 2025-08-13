<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bodega;

class BodegaSeeder extends Seeder
{
    public function run(): void
    {
        Bodega::factory()->count(10)->create();
    }
}
