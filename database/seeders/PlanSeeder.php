<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'codigo' => 'starter',
                'nombre' => 'Starter',
                'precio_mensual' => 0,
                'precio_anual' => 0,
                'trial_dias' => 14,
                'limites' => [
                    'usuarios_max' => 1,
                    'locales_max' => 1,
                ],
                'features' => ['basic'],
                'activo' => true,
            ],
            [
                'codigo' => 'pro',
                'nombre' => 'Pro',
                'precio_mensual' => 39.00,
                'precio_anual' => 390.00,
                'trial_dias' => 14,
                'limites' => [
                    'usuarios_max' => 10,
                    'locales_max' => 2,
                    'impresoras_max' => 4,
                    'kds_screens_max' => 2,
                    'productos_max' => 2000,
                    'api_calls_por_dia' => 5000,
                    'storage_gb' => 5,
                ],
                'features' => ['kds','promociones','analytics','export'],
                'activo' => true,
            ],
            [
                'codigo' => 'enterprise',
                'nombre' => 'Enterprise',
                'precio_mensual' => 99.00,
                'precio_anual' => 990.00,
                'trial_dias' => 30,
                'limites' => null,
                'features' => ['todo'],
                'activo' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['codigo' => $plan['codigo']], $plan);
        }
    }
}
