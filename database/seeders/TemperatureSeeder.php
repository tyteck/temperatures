<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Temperature;
use Carbon\CarbonImmutable;
use Database\Seeders\Traits\Truncatable;
use Illuminate\Database\Seeder;

class TemperatureSeeder extends Seeder
{
    use Truncatable;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateTable('temperatures');

        $departements = Departement::query()->get();

        $startDate = CarbonImmutable::today()->subDays($departements->count());
        $index     = 1;
        $data      = $departements->map(function (Departement $departement) use (&$index, $startDate) {
            return [
                'id'               => $index++,
                'departement_id'   => $departement->id,
                'temperature_moy'  => fake()->randomFloat(2, -20.00, 45.00),
                'temperature_min'  => fake()->randomFloat(2, -20.00, 45.00),
                'temperature_max'  => fake()->randomFloat(2, -20.00, 45.00),
                'date_observation' => $startDate->addDays($index),
                'created_at'       => now(),
            ];
        });

        Temperature::insert($data->toArray());
    }
}
