<?php

declare(strict_types=1);

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Exceptions\InvalidDatasetException;
use App\Service\ProcessDatasetService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ProcessDatasetServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function single_dataset_should_be_recorded_properly(): void
    {
        $dataset = File::get(fixtures_path('single_dataset.json'));

        ProcessDatasetService::from($dataset)->store();

        $this->assertDatabaseCount('temperatures', 1);
        $this->assertDatabaseCount('departements', 1);

        $this->assertDatabaseHas('temperatures', [
            'temperature_moy' => 14.25,
            'temperature_min' => 9.8,
            'date_observation' => Carbon::createFromFormat('Y-m-d', '2019-10-23'),
            'temperature_max' => 18.7,
        ]);

        $this->assertDatabaseHas('departements', [
            'nom' => "Côte-d'Or",
            'code_insee' => 21,
        ]);
    }

    /** @test */
    public function small_dataset_should_be_recorded_properly(): void
    {
        $dataset = File::get(fixtures_path('small_dataset.json'));

        ProcessDatasetService::from($dataset)->store();

        $this->assertDatabaseCount('temperatures', 17);
        $this->assertDatabaseCount('departements', 15);

        $this->assertDatabaseHas('temperatures', [
            'temperature_moy' => 14.25,
            'temperature_min' => 9.8,
            'date_observation' => Carbon::createFromFormat('Y-m-d', '2019-10-23'),
            'temperature_max' => 18.7,
        ]);

        $this->assertDatabaseHas('departements', [
            'nom' => "Côte-d'Or",
            'code_insee' => 21,
        ]);
    }

    /** @test */
    public function empty_dataset_should_throw_exception(): void
    {
        $dataset = '';
        $this->expectException(InvalidDatasetException::class);
        $this->expectExceptionMessage('Dataset is empty.');
        ProcessDatasetService::from($dataset)->store();
    }
}
