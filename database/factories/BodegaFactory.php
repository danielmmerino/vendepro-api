<?php

namespace Database\Factories;

use App\Models\Bodega;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Bodega> */
class BodegaFactory extends Factory
{
    protected $model = Bodega::class;

    public function definition(): array
    {
        return [
            'local_id' => 1,
            'nombre' => $this->faker->company(),
            'es_principal' => $this->faker->boolean(),
        ];
    }
}
