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
            'temperature_moy' => fake()->randomFloat(2, min: $temperatureMin, max: $temperatureMax),
            'temperature_min' => $temperatureMin,
            'temperature_max' => $temperatureMax,
            'date_observation' => Carbon::now(),
        ];
    }

    public function departement(Departement $departement): static
    {
        return $this->state(['departement_id' => $departement->id]);
    }

    public function dateObservation(Carbon $dateObservation)
    {
        return $this->state(['date_observation' => $dateObservation]);
    }
}
