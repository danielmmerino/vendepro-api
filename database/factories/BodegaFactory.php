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
            'codigo' => $this->faker->unique()->regexify('BOD-[0-9]{3}'),
            'nombre' => $this->faker->company(),
            'estado' => $this->faker->randomElement(['activa','inactiva']),
        ];
    }
}
