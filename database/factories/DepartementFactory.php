<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Departement>
 */
class DepartementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->state(),
            'code_insee' => fake()->numerify('###'),
        ];
    }

    public function name(string $name): static
    {
        return $this->state(['nom' => $name]);
    }

    public function codeInsee(string $codeInsee): static
    {
        return $this->state(['code_insee' => $codeInsee]);
    }
}
