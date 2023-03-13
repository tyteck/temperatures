<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Departement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Temperature>
 */
class TemperatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $temperatureMin = fake()->randomFloat(2, -10, 45);
        $temperatureMax = fake()->randomFloat(2, $temperatureMin, 55);

        return [
            'departement_id' => Departement::factory(),
            'temperature_moyenne' => fake()->randomFloat(2),
            'temperature_min' => $temperatureMin,
            'temperature_max' => $temperatureMax,
            'date_observation' => Carbon::now(),
        ];
    }
}
